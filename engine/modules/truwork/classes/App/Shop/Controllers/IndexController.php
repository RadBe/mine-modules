<?php


namespace App\Shop\Controllers;


use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Shop\Entity\Category;
use App\Shop\Traits\NeedCategory;

class IndexController extends Controller
{
    use NeedServer, NeedCategory;

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $servers = array_map(function ($server) {
            return $server->toArray();
        }, $this->getServersModel()->getEnabled());

        $this->compile('shop/index.tpl', [
            'servers' => htmlspecialchars(json_encode($servers)),
            'settings' => htmlspecialchars(json_encode([
                'url' => [
                    'search' => ajax_url('shop', 'catalog', 'search'),
                    'buy' => ajax_url('shop', 'catalog', 'buy'),
                    'warehouse' => ajax_url('shop', 'warehouse', 'warehouse'),
                    'history' => ajax_url('shop', 'warehouse', 'history')
                ],
                'enchants' => $this->getModule()->getConfig()->getEnchants()
            ])),
            'categories' => htmlspecialchars(json_encode(array_map(function (Category $category) {
                return $category->toArray();
            }, $this->getCategoryModel()->getAll()))),
            'user' => htmlspecialchars(json_encode($request->user()->toArray())),
            'csrf' => tw_csrf(),
            'server' => $request->get('server'),
            'category' => $request->get('category'),
            'product' => $request->get('product'),
        ]);
    }
}
