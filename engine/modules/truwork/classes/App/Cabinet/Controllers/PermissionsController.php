<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Events\PermissionsBuyEvent;
use App\Cabinet\Middleware\CheckModuleEnabled;
use App\Cabinet\Module;
use App\Core\Application;
use App\Core\Exceptions\Exception;
use App\Core\Exceptions\NotEnoughMoneyException;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Http\Traits\NeedUser;
use Respect\Validation\Validator;

class PermissionsController extends Controller
{
    use NeedServer, NeedUser;

    /**
     * @inheritDoc
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->middleware(new CheckModuleEnabled($module->getConfig()), 'perms');
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function buy(Request $request)
    {
        $request->checkCsrf();

        $request->validate(Validator::key('perm', Validator::stringType()));
        $perm = $request->post('perm');

        $cabinetPermissions = $this->getModule()->getConfig()->getPermissions();
        if (!isset($cabinetPermissions[$perm]) || $cabinetPermissions[$perm]['price'] < 1) {
            throw new Exception('Это право не продается!');
        }

        $server = null;
        if ($cabinetPermissions[$perm]['need_server']) {
            $server = $this->getServer($request);
        }

        if ($request->user()->hasPermission($server, $perm)) {
            throw new Exception('У вас уже есть это право.');
        }

        if (!$request->user()->hasMoney($cabinetPermissions[$perm]['price'])) {
            throw new NotEnoughMoneyException($cabinetPermissions[$perm]['price']);
        }

        $request->user()->withdrawMoney($cabinetPermissions[$perm]['price']);
        $request->user()->addPermission($server, $perm);
        $this->getUserModel()->update($request->user());

        dispatch(new PermissionsBuyEvent($request->user(), $server, $cabinetPermissions[$perm]['price'], $perm));

        $this->printJsonResponse(true, 'Успех!', 'Вы успешно купили право ' . $cabinetPermissions[$perm]['name'], [
            'balance' => $request->user()->getMoney(),
            'perms' => $request->user()->getPermissions()
        ]);
    }
}
