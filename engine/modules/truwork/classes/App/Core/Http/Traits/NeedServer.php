<?php


namespace App\Core\Http\Traits;


use App\Core\Entity\Server;
use App\Core\Exceptions\ServerNotFoundException;
use App\Core\Http\Request;
use App\Core\Models\ServersModel;
use Respect\Validation\Validator;

trait NeedServer
{
    /**
     * @var ServersModel
     */
    protected $serversModel;

    /**
     * @return ServersModel
     */
    public function getServersModel(): ServersModel
    {
        return is_null($this->serversModel)
            ? $this->serversModel = $this->app->make(ServersModel::class)
            : $this->serversModel;
    }

    /**
     * @param Request $request
     * @param bool $throwIfNotFound
     * @param bool $enabled
     * @return Server|null
     * @throws ServerNotFoundException
     */
    public function getServer(Request $request, bool $throwIfNotFound = true, bool $enabled = true): ?Server
    {
        $request->validateAny(Validator::key('server', Validator::intVal()));

        $serverId = (int) $request->any('server');
        if ($throwIfNotFound) {
            return optional($this->getServersModel()->find($serverId, $enabled))
                ->getOrFail(new ServerNotFoundException($serverId));
        }

        return $this->getServersModel()->find($serverId, $enabled);
    }
}
