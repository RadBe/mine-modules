<?php


namespace App\Cabinet\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class UnbanController extends AdminController
{
    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveSettings(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('price', Validator::intVal()->min(1))
        );

        $price = (int) $request->post('price');
        $this->module->getConfig()->setUnbanPrice($price);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки сохранены.')
            ->withBack(admin_url('cabinet','settings', 'index',  ['tab' => 'unban']))
            ->render();
    }
}
