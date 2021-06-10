<?php


namespace App\Shop\Controllers;


use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Shop\Entity\Warehouse;
use App\Shop\Models\WarehouseModel;

class WarehouseController extends Controller
{
    use NeedServer;

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\Exception
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function warehouse(Request $request)
    {
        $server = $this->getServer($request);

        /* @var WarehouseModel $warehouseModel */
        $warehouseModel = $this->app->make(WarehouseModel::class);
        $items = $warehouseModel->getUserItems($request->user(), $server, false);

        $this->printJsonData([
            'items' => array_map(function (Warehouse $item) {
                return $item->toArray();
            }, $items->getResult()),
            'pagination' => $items->paginationData()
        ]);
        die;
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\Exception
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function history(Request $request)
    {
        $server = $this->getServer($request);

        /* @var WarehouseModel $warehouseModel */
        $warehouseModel = $this->app->make(WarehouseModel::class);
        $items = $warehouseModel->getUserItems($request->user(), $server, null);

        $this->printJsonData([
            'items' => array_map(function (Warehouse $item) {
                return $item->toArray();
            }, $items->getResult()),
            'pagination' => $items->paginationData()
        ]);
        die;
    }
}
