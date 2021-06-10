<?php


namespace App\Referal\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class SettingsController extends AdminController
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $this->createView('Основные настройки')
            ->addBreadcrumb($this->module->getName(), admin_url('referal'))
            ->addBreadcrumb('Основные настройки')
            ->render('referal/settings/index', [
                'rate' => $this->module->getConfig()->getReferalRate(),
                'tab' => $request->get('tab', 'home')
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function save(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('rate', Validator::numericVal()->between(0, 100))
        );

        $rate = (int) $request->post('rate');
        $this->module->getConfig()->setReferalRate($rate);
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки сохранены.')
            ->withBack(admin_url('referal', 'settings'))
            ->render();
    }
}
