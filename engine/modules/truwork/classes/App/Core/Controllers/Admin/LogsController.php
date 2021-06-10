<?php


namespace App\Core\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Http\Traits\NeedUser;
use App\Core\Models\LogModel;
use App\Core\Support\AttachRelationEntity;

class LogsController extends AdminController
{
    use NeedServer, NeedUser;

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\Exception
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function index(Request $request)
    {
        /* @var LogModel $logModel */
        $logModel = $this->app->make(LogModel::class);

        $user = $server = null;
        if (!empty($request->any('user'))) {
            $user = $this->getUser($request);
        }

        if (!empty($request->any('server'))) {
            $server = $this->getServer($request);
        }

        $logs = $logModel->search(optional($user)->getId(), optional($server)->id);
        AttachRelationEntity::make($logs->getResult(), $this->getUserModel(), 'user_id');
        AttachRelationEntity::make($logs->getResult(), $this->getServersModel(), 'server_id');

        $view = $this->createView('Лог действий на сайте');

        if (!empty($user) || !empty($server)) {
            $view->addBreadcrumb('Логи', admin_url('core', 'logs'))
                ->addBreadcrumb('Поиск');
        } else {
            $view->addBreadcrumb('Логи');
        }
        $view->render('logs', [
            'servers' => $this->getServersModel()->getEnabled(),
            'logs' => $logs,
            'search' => [
                'server' => optional($server)->id,
                'user' => optional($user)->name
            ]
        ]);
    }
}
