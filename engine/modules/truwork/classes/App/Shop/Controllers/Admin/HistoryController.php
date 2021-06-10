<?php


namespace App\Shop\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Http\Traits\NeedUser;
use App\Shop\Models\WarehouseModel;

class HistoryController extends AdminController
{
    use NeedUser, NeedServer;

    public function index(Request $request)
    {
        $user = empty($request->get('user')) ? null : $this->getUser($request);
        $server = empty($request->get('server')) ? null : $this->getServer($request);

        /* @var WarehouseModel $warehouseModel */
        $warehouseModel = $this->app->make(WarehouseModel::class);

        $view = $this->createView('История покупок')
            ->addBreadcrumb($this->module->getName(), admin_url('shop'));

        if (!is_null($server) || !is_null($user)) {
            $view
                ->addBreadcrumb('История покупок', admin_url('shop', 'history'))
                ->addBreadcrumb('Поиск');
        } else {
            $view->addBreadcrumb('История покупок');
        }

        $view->render('shop/history', [
            'history' => $warehouseModel->getHistory($user, $server),
            'search' => [
                'server' => optional($server)->id,
                'user' => optional($user)->name
            ]
        ]);
    }
}
