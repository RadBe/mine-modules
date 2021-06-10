<?php


namespace App\Core\Game\Permissions;


use App\Core\Application;
use App\Core\Database\DB;
use App\Core\Entity\Server;

class PermissionsManager
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $permissions = [];

    /**
     * PermissionsManager constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param Server $server
     * @return Permissions
     * @throws \App\Core\Exceptions\ConnectionException
     * @throws \App\Core\Exceptions\ConnectionNotFoundException
     */
    private function getDefault(Server $server): Permissions
    {
        if (!isset($this->permissions['*'])) {
            $class = $this->app->getConfig()->getPermissionsManagerClass();
            $this->permissions['*'] = $this->app->make($class, DB::getConnection('server_' . $server->id), $server);
        }

        return $this->permissions['*'];
    }

    /**
     * @param Server $server
     * @return Permissions
     * @throws \App\Core\Exceptions\ConnectionException
     * @throws \App\Core\Exceptions\ConnectionNotFoundException
     */
    private function getServer(Server $server): Permissions
    {
        if (!isset($this->permissions[$server->getId()])) {
            $class = $server->plugin_permissions;
            if (is_null($class)) {
                $this->permissions[$server->getId()] = $this->getDefault($server);
            } elseif($class == LuckPerms::class) {
                $this->permissions['luckperms'] =
                    $this->app->make($class, DB::getConnection('luckperms'), $server);
            } else {
                $this->permissions[$server->getId()] =
                    $this->app->make($class, DB::getServerConnection($server->id), $server);
            }
        }

        return $this->permissions[$server->getId()];
    }

    /**
     * @param Server $server
     * @return Permissions
     */
    public function getPermissions(Server $server): Permissions
    {
        return $this->getServer($server);
    }
}
