<?php


namespace App\Banlist\Controllers;


use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Pagination\PaginatedResult;
use App\Core\Services\SkinManager;

class IndexController extends Controller
{
    /**
     * @return void
     */
    public function index()
    {
        $this->meta->setTitle('Список забаненных игроков');
        $this->compileWithCache('banlist-index', function () {
            $this->createView('banlist/index.tpl', [
                'heads_url' => base_url('core', 'skin', 'view', ['mode' => SkinManager::MODE_HEAD]),
                'ajax_url' => ajax_url('banlist', 'index', 'ajax')
            ])
                ->if('bootstrap', $this->app->getConfig()->useBootstrap())
                ->compile();
        });
    }

    /**
     * @param Request $request
     */
    public function ajax(Request $request)
    {
        $searchUser = preg_replace('/[^A-Za-zА-Яа-яЁё0-9_\- ]/u', '', $request->any('user', ''));

        $config = $this->getModule()->getConfig();
        /* @var PaginatedResult $bans */
        $bans = $this->app->make($config->getModel())->getAll(
            true,
            $this->getModule()->getConfig()->getPerPage(),
            trim($searchUser)
        );

        $this->printJsonData([
            'rows' => array_map(function ($ban) {
                /* @var \App\Banlist\Entity\Ban $ban */
                return $ban->toArray();
            }, $bans->getResult()),
            'pagination' => $bans->paginationData()
        ]);
    }
}
