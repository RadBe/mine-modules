<?php


namespace App\Cabinet\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class GameMoneyController extends AdminController
{
    use NeedServer;

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function save(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('plugins', Validator::arrayType())
                ->key('rate_money', Validator::numericVal()->min(1))
        );
        $plugins = $request->post('plugins');
        $rateMoney = (int) $request->post('rate_money');

        foreach ($this->getServersModel()->getEnabled() as $server)
        {
            if (isset($plugins[$server->id])) {
                $class = $plugins[$server->id];
                if (class_exists($class)) {
                    $server->plugin_g_money = $class;
                } else {
                    $server->plugin_g_money = null;
                }
            } else {
                $server->plugin_g_money = null;
            }

            $this->getServersModel()->update($server);
        }

        $this->module->getConfig()->setGameMoneyRate($rateMoney);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки игровых денег сохранены')
            ->withBack(admin_url('cabinet', 'settings', 'index', ['tab' => 'game-money']))
            ->render();
    }
}
