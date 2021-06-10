<?php


namespace App\Monitoring\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\View\AdminAlert;

class IndexController extends AdminController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->createAlert(AdminAlert::MSG_TYPE_INFO, 'Мониторинг', 'Здесь ничего нет')
            ->withBack(admin_url('core'))
            ->render();
    }
}
