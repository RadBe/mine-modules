<?php


namespace App\Core\Game\Money;


use App\Core\Application;
use App\Core\Entity\Server;
use App\Core\Exceptions\Exception;
use App\Core\Game\Money\Models\GameMoneyModel;

class GameMoneyManager
{
    /**
     * @param Server $server
     * @return GameMoneyModel
     * @throws Exception
     */
    public function getGameMoneyModel(Server $server): GameMoneyModel
    {
        if (is_null($server->plugin_g_money)) {
            throw new Exception(sprintf('На сервере %s не настроен плагин с монетами!', $server->name));
        }

        return Application::getInstance()
            ->make($server->plugin_g_money::MODEL, Application::getInstance(), 'server_' . $server->id);
    }
}
