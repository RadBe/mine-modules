<?php


namespace App\Banlist\Controllers\Admin;


use App\Core\Cache\Cache;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class SettingsController extends AdminController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->createView($this->module->getName())
            ->addBreadcrumb($this->module->getName(), admin_url('banlist'))
            ->addBreadcrumb('Настройки')
            ->render('banlist/settings', [
                'table' => $this->module->getConfig()->getTable(),
                'perPage' => $this->module->getConfig()->getPerPage(),
                'selectedPlugin' => $this->module->getConfig()->getPlugin(),
                'plugins' => $this->module->getConfig()->getPlugins()
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function save(Request $request)
    {
        $request->checkCsrf();

        $config = $this->module->getConfig();

        $request->validate(
            Validator::key('table', Validator::stringType()->length(1, 40)->regex('/^[A-Za-z0-9\_]+$/'))
                ->key('per_page', Validator::numericVal()->between(1, 1000))
                ->key('plugin', Validator::stringType()->in(array_keys($config->getPlugins())))
        );

        $oldTable = $config->getTable();
        $config->setTable(strip_tags($request->post('table')));
        $config->setPerPage((int) $request->post('per_page'));
        if ($config->getPlugin() != $request->post('plugin') && $oldTable == $config->getTable()) {
            $config->setTable('');
        }
        $config->setPlugin($request->post('plugin'));

        $this->updateModule();

        Cache::forget('truwork_modules');

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки сохранены.')
            ->withBack(admin_url('banlist', 'settings'))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function addPlugin(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('instance', Validator::stringType()->length(1, 255))
        );

        $instance = '\\' . ltrim(strip_tags($request->post('instance')), '\\');

        if (!class_exists($instance)) {
            throw new Exception('Класс плагина не найден.');
        }

        $name = (new $instance)->plugin();

        $config = $this->module->getConfig();

        if (isset($config->getPlugins()[$name])) {
            throw new Exception('Такой плагин уже есть.');
        }

        $config->addPlugin($name, $instance);
        $this->updateModule();

        Cache::forget('truwork_modules');

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Плагин банов добавлен.')
            ->withBack(admin_url('banlist', 'settings'))
            ->render();
    }
}
