<?php


namespace App\Shop\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\View\AdminAlert;
use App\Shop\Enchanting\Enchant;
use Respect\Validation\Validator;

class SettingsController extends AdminController
{
    use NeedServer;

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $this->createView('Основные настройки')
            ->addBreadcrumb($this->module->getName(), admin_url('shop'))
            ->addBreadcrumb('Основные настройки')
            ->render('shop/settings/index', [
                'settings' => [
                    'limit' => $this->module->getConfig()->getLimit()
                ],
                'servers' => $this->getServersModel()->getEnabled(),
                'enchants' => $this->module->getConfig()->getEnchants(),
                'tab' => $request->get('tab', 'home')
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveSettings(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('limit', Validator::numericVal()->min(1))
        );

        $limit = (int) $request->post('limit');
        $this->module->getConfig()->setLimit($limit);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки сохранены.')
            ->withBack(admin_url('shop', 'settings', 'index'))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function addEnchant(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('id', Validator::numericVal())
                ->key('name', Validator::stringType())
        );

        $server = empty($request->post('server')) ? null : $this->getServer($request);
        $this->module->getConfig()
            ->addEnchant(new Enchant((int) $request->post('id'), htmlspecialchars($request->post('name'))), $server);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Зачар добавлен.')
            ->withBack(admin_url('shop', 'settings', 'index', ['tab' => 'enchants']))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function removeEnchant(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('id', Validator::numericVal())
        );

        $server = empty($request->post('server')) ? null : $this->getServer($request);
        $enchant = $this->module->getConfig()->getEnchant((int) $request->post('id'), optional($server)->id);
        $this->module->getConfig()->removeEnchant($enchant, $server);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Зачар удален.')
            ->withBack(admin_url('shop', 'settings', 'index', ['tab' => 'enchants']))
            ->render();
    }
}
