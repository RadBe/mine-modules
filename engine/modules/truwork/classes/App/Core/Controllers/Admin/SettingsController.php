<?php


namespace App\Core\Controllers\Admin;


use App\Core\Cache\Cache;
use App\Core\Database\DB;
use App\Core\Exceptions\ConnectionException;
use App\Core\Exceptions\ConnectionNotFoundException;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Services\SkinManager;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class SettingsController extends AdminController
{
    /**
     * @return void
     */
    public function index()
    {
        $config = $this->app->getConfig();

        $this->createView('Общие настройки')
            ->addBreadcrumb('Общие настройки')
            ->render('settings', [
                'moneyColumn' => $config->getMoneyColumn(),
                'permissionsPlugins' => $config->getPermissionsManagers(),
                'permissionPluginClass' => $config->getPermissionsManagerClass(),
                'uuidGenerators' => $config->getUUIDGenerators(),
                'uuidGeneratorClass' => $config->getUUIDGeneratorClass(),
                'skin' => SkinManager::getDirectory(false) . '/skins/default.png?' . time(),
                'bootstrap' => $config->useBootstrap()
            ]);
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveSettings(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('money_column', Validator::stringType()->length(1, 40)->regex('/^[A-Za-z0-9_\-]+$/'))
                ->key('permissions_manager', Validator::stringType())
                ->key('uuid_generator', Validator::stringType())
                ->key('bootstrap', Validator::boolVal(), false)
        );

        $config = $this->app->getConfig();

        $config->setMoneyColumn($request->post('money_column'));

        $plugin = $request->post('permissions_manager');
        if (!class_exists($plugin)) {
            throw new Exception('Выбранный плагин на права не найден!');
        }

        $uuidGenerator = $request->post('uuid_generator');
        if (!class_exists($uuidGenerator)) {
            throw new Exception('Выбранный способ генерации uuid не найден!');
        }

        $config->setPermissionsManagerClass($plugin);
        $config->setUUIDGenerator($uuidGenerator);
        $config->setUseBootstrap((bool) $request->post('bootstrap', false));

        $this->updateModule();

        Cache::forget('truwork_modules');

        $defaultSkin = $request->image('skin');
        if (!is_null($defaultSkin)) {
            $directory = SkinManager::getDirectory(true);
            if (!is_dir($directory . '/skins')) {
                mkdir($directory . '/skins', 0777);
            }

            $defaultSkin->validate(Validator::mimetype('image/png'));
            if (empty($defaultSkin->move($directory . '/skins', 'default.png'))) {
                $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Предупреждение!', 'Не удалось сохранить картинку дефолтного скина.')
                    ->withBack(admin_url('core', 'settings'))
                    ->render();
            }

            SkinManager::clearCache('default');
        }

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Настройки сохранены.')
            ->withBack(admin_url('core', 'settings'))
            ->render();
    }

    /**
     * @return void
     */
    public function luckperms()
    {
        $db = [];
        try {
            $db = DB::getConfig()->getConnectionData('luckperms');
        } catch (ConnectionNotFoundException $exception){}
        $this->createView('Настройки LuckPerms')
            ->addBreadcrumb('Настройки LuckPerms')
            ->render('luckperms', [
                'db' => $db
            ]);
    }

    /**
     * @param Request $request
     * @throws ConnectionNotFoundException
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveLuckPerms(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key(
                'db', Validator::arrayType()->key('host', Validator::ip())
                ->key('user', Validator::stringType())
                ->key('password', Validator::stringType())
                ->key('dbname', Validator::stringType())
            )
        );

        $db = $request->post('db');
        $dbConfig = DB::getConfig();
        $dbConfig->addConnection('luckperms', $db['host'], $db['user'], $db['password'], $db['dbname']);
        try {
            DB::getConnection('luckperms');
        } catch (ConnectionException $exception) {
            $this->createAlert(AdminAlert::MSG_TYPE_ERROR, 'Возникла ошибка!', $exception->getMessage())
                ->withBack(admin_url('core', 'settings', 'luckperms'))
                ->render();
        }
        $dbConfig->save();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Настройки сохранены.')
            ->withBack(admin_url('core', 'settings', 'luckperms'))
            ->render();
    }
}
