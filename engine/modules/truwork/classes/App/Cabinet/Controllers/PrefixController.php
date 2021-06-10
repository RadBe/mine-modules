<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Events\PrefixSetEvent;
use App\Cabinet\Middleware\CheckModuleEnabled;
use App\Cabinet\Module;
use App\Core\Application;
use App\Core\Cache\Cache;
use App\Core\Exceptions\Exception;
use App\Core\Game\Permissions\PermissionsManager;
use App\Core\Game\Permissions\PrefixSuffix;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use Respect\Validation\Validator;

class PrefixController extends Controller
{
    use NeedServer;

    /**
     * @var PermissionsManager
     */
    private $permissionsManager;

    /**
     * @inheritDoc
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->middleware(new CheckModuleEnabled($module->getConfig()), 'prefix');

        $this->permissionsManager = $app->make(PermissionsManager::class);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function load(Request $request)
    {
        $request->checkCsrf();

        $server = $this->getServer($request);
        $prefixSuffix = Cache::remember('cabinet_prefix' . $request->user()->getId() . $server->getId(), function () use ($server) {
            return $this->permissionsManager->getPermissions($server)->getPrefixSuffix($this->app->getUser());
        }, 30);

        $this->printJsonResponse(true, '', '', $prefixSuffix->toArray());
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function save(Request $request)
    {
        $request->checkCsrf();

        $colors = $this->module->getConfig()->getColors();
        $min = $this->module->getConfig()->getPrefixMin();
        $max = $this->module->getConfig()->getPrefixMax();
        $regex = $this->module->getConfig()->getPrefixRegex();

        $request->validate(
            Validator::key('prefix_color', Validator::stringType()->in($colors))
                ->key('nick_color', Validator::stringType()->in($colors))
                ->key('text_color', Validator::stringType()->in($colors))
                ->key('text', Validator::stringType()->length($min, $max)->regex("/^[$regex]+$/"), $min > 0)
        );

        $server = $this->getServer($request);

        $groups = $this->module->getConfig()->getPrefixGroups();
        if (!$this->user()->hasPermissionOrGroups($server, 'prefix', $groups)) {
            throw new Exception('Недостаточно прав для изменения префикса.');
        }

        $prefix = new PrefixSuffix(
            $request->post('prefix_color'),
            htmlspecialchars($request->post('text', '')),
            $request->post('nick_color'),
            $request->post('text_color')
        );

        $this->permissionsManager->getPermissions($server)->setPrefixSuffix($request->user(), $prefix);

        Cache::forget('cabinet_prefix' . $request->user()->getId() . $server->getId());
        dispatch(new PrefixSetEvent($request->user(), $server, $prefix));
        $this->printJsonResponse(true, 'Успех!', 'Префикс сохранен.', [
            $prefix->toArray()
        ]);
    }
}
