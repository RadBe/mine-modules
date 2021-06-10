<?php


namespace App\Core\Controllers\Admin;


use App\Core\Http\Middleware\HasModule;
use App\Core\Module;
use App\Cabinet\Services\UserGroupService;
use App\Core\Application;
use App\Core\Cache\Cache;
use App\Core\Entity\User;
use App\Core\Exceptions\Exception;
use App\Core\Game\Permissions\PermissionsManager;
use App\Core\Game\Permissions\PrefixSuffix;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Http\Traits\NeedUser;
use App\Core\Services\SkinManager;
use App\Core\View\AdminAlert;
use DateTimeImmutable;
use Respect\Validation\Validator;

class PlayersController extends AdminController
{
    use NeedUser, NeedServer;

    /**
     * @var \App\Cabinet\Config
     */
    private $cabinetConfig;

    /**
     * PlayersController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        if ($app->hasModule('cabinet')) {
            $this->cabinetConfig = $app->getModule('cabinet')->getConfig();
        }

        $this->middleware(HasModule::class, 'cabinet')->only(
            'addPermission', 'removePermission', 'addGroup', 'removeGroup', 'deleteSkin', 'deleteCloak', 'savePrefix', 'removePrefix'
        );
    }

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $this->createView('Управление игроками')
            ->addBreadcrumb('Управление игроками')
            ->render(
                'core/players', [
                    'username' => htmlspecialchars($request->get('username')),
                    'servers' => $this->getServersModel()->getEnabled(),
                ]
            );
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function player(Request $request)
    {
        /* @var User $user */
        $user = $this->getUser($request);
        $server = $this->getServer($request);

        $hasModuleCabinet = $this->app->hasModule('cabinet');
        $hasModuleTopVotes = $this->app->hasModule('top-votes');
        $hasModuleReferal = $this->app->hasModule('referal');
        $data = [
            'hasModuleCabinet' => $hasModuleCabinet,
            'hasModuleTopVotes' => $hasModuleTopVotes,
            'hasModuleReferal' => $hasModuleReferal,
            'server' => $server,
            'colors' => $this->app->getConfig()->getColors(),
            'tab' => $request->get('tab', 'home'),
            'user' => \App\Cabinet\Entity\User::swap($user),
        ];

        if ($hasModuleCabinet) {
            /* @var PermissionsManager $permissionsManager */
            $permissionsManager = $this->app->make(PermissionsManager::class);
            $data = array_merge($data, [
                'perms' => $this->cabinetConfig->getPermissions(),
                'groups' => $this->cabinetConfig->getGroupsArray(),
                'prefix' => $permissionsManager->getPermissions($server)->getPrefixSuffix($user),
                'prefixConfig' => [
                    'max' => $this->cabinetConfig->getPrefixMax(),
                    'regex' => $this->cabinetConfig->getPrefixRegex()
                ],
            ]);
        }

        if ($hasModuleTopVotes) {
            $topVotesUser = \App\TopVotes\Entity\User::swap($user);
            $data = array_merge($data, [
                'bonuses' => $topVotesUser->getBonuses(),
                'votes' => $topVotesUser->getVotes()
            ]);
        }

        if ($hasModuleReferal) {
            $data = array_merge($data, [
                'referals' => $this->getUserModel()->getReferals($user),
                'referer' => is_null($user->referer_id) ? null : $this->getUserModel()->find($user->referer_id)
            ]);
        }

        $this->createView($this->module->getName())
            ->addBreadcrumb('Управление игроками', admin_url($this->module->getId(), 'players', 'index', ['username' => $user->getName()]))
            ->addBreadcrumb($user->getName() . ' (' . $server->name . ')')
            ->render('core/player/index', $data);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws Exception
     */
    public function addPermission(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('perm', Validator::stringType()->in(array_keys($this->cabinetConfig->getPermissions())))
        );

        $perm = $request->post('perm');

        $user = $this->getUser($request);
        $server = $this->getServer($request);

        $user->addPermission($this->cabinetConfig->getPermissions()[$perm]['need_server'] ? $server : null, $perm);
        $this->getUserModel()->updatePermissions($user);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Право добавлено.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'perms', 'user' => $user->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function removePermission(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('perm', Validator::stringType()->in(array_keys($this->cabinetConfig->getPermissions())))
        );

        $user = $this->getUser($request);
        $server = $this->getServer($request);

        $user->removePermission($server, $request->post('perm'));
        $this->getUserModel()->updatePermissions($user);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Право удалено.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'perms', 'user' => $user->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \Exception
     */
    public function addGroup(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('group', Validator::stringType()->in(array_keys($this->cabinetConfig->getGroupsArray())))
                ->key('expiry', Validator::emptyable(Validator::dateTime()))
        );

        $expiry = trim($request->post('expiry', ''));
        $expiry = empty($expiry) ? 0 : (new DateTimeImmutable($expiry))->getTimestamp();
        $user = \App\Cabinet\Entity\User::swap($this->getUser($request));
        $server = $this->getServer($request);
        $group = $this->cabinetConfig->getGroup($request->post('group'));

        $service = new UserGroupService($this->cabinetConfig);
        $service->setGroup($user, $group, $server, $expiry);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Группа выдана.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'groups', 'user' => $user->getUser()->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function removeGroup(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('group', Validator::stringType()->in(array_keys($this->cabinetConfig->getGroupsArray())))
        );

        $user = \App\Cabinet\Entity\User::swap($this->getUser($request));
        $server = $this->getServer($request);

        $group = $this->cabinetConfig->getGroup($request->post('group'));

        $user->getGroupManager()->removeGroup($server, $group->getName());

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Группа удалена.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'groups', 'user' => $user->getUser()->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function deleteSkin(Request $request)
    {
        $request->checkCsrf();

        $user = $this->getUser($request);
        $server = $this->getServer($request);

        $skin = SkinManager::getSkinFile($user->getName());
        if (!is_file($skin)) {
            $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Ошибка!', 'Игрок еще не загружал скин.')
                ->withBack(admin_url('core', 'players', 'player', [
                    'tab' => 'skin', 'user' => $user->getName(), 'server' => $server->getId()
                ]))
                ->render();
        }

        unlink($skin);
        SkinManager::clearCache($user->getName());

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Скин удален.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'skin', 'user' => $user->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function deleteCloak(Request $request)
    {
        $request->checkCsrf();

        $user = $this->getUser($request);
        $server = $this->getServer($request);

        $cloak = SkinManager::getCloakFile($user->getName());
        if (!is_file($cloak)) {
            $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Ошибка!', 'Игрок еще не загружал плащ.')
                ->withBack(admin_url('core', 'players', 'player', [
                    'tab' => 'skin', 'user' => $user->getName(), 'server' => $server->getId()
                ]))
                ->render();
        }

        unlink($cloak);
        SkinManager::clearCache($user->getName());

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Плащ удален.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'skin', 'user' => $user->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function savePrefix(Request $request)
    {
        $request->checkCsrf();

        $user = $this->getUser($request);
        $server = $this->getServer($request);

        $colors = array_keys($this->app->getConfig()->getColors());
        $min = $this->cabinetConfig->getPrefixMin();
        $max = $this->cabinetConfig->getPrefixMax();
        $regex = $this->cabinetConfig->getPrefixRegex();

        $request->validate(
            Validator::key('prefix_color', Validator::stringType()->in($colors))
                ->key('nick_color', Validator::stringType()->in($colors))
                ->key('text_color', Validator::stringType()->in($colors))
                ->key('text', Validator::stringType()->length(0, $max)->regex("/^[$regex]+$/"), $min > 0)
        );

        $prefix = new PrefixSuffix(
            $request->post('prefix_color'),
            $request->post('text', ''),
            $request->post('nick_color'),
            $request->post('text_color')
        );

        $permissionsManager = new PermissionsManager($this->app);

        $permissionsManager->getPermissions($server)->setPrefixSuffix($user, $prefix);

        Cache::forget('cabinet_prefix' . $server->getId());

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Префикс изменен.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'prefix', 'user' => $user->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function removePrefix(Request $request)
    {
        $request->checkCsrf();

        $user = $this->getUser($request);
        $server = $this->getServer($request);

        $permissionsManager = new PermissionsManager($this->app);

        $permissionsManager->getPermissions($server)->removePrefixSuffix($user);

        Cache::forget('cabinet_prefix' . $server->getId());

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Префикс удален.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'prefix', 'user' => $user->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveInfo(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('money', Validator::numericVal())
        );

        $user = $this->getUser($request);
        $server = $this->getServer($request);

        if ($this->app->hasModule('top-votes')) {
            $request->validate(
                Validator::key('bonuses', Validator::numericVal())
                    ->key('votes', Validator::numericVal())
            );

            $topVotesUser = \App\TopVotes\Entity\User::swap($user);
            $topVotesUser->setBonuses((int) $request->post('bonuses', 0));
            $topVotesUser->setVotes((int) $request->post('votes', 0));
            $this->app->make(\App\TopVotes\Models\UserModel::class)->updateEntity($topVotesUser);
        }

        $user->setMoney((int) $request->post('money'));
        $this->getUserModel()->updateBalance($user);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Информация сохранена.')
            ->withBack(admin_url('core', 'players', 'player', [
                'tab' => 'home', 'user' => $user->getName(), 'server' => $server->getId()
            ]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function removeReferal(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('referal', Validator::numericVal())
                ->key('server', Validator::numericVal())
        );

        $server = (int) $request->post('server');
        $user = $this->getUser($request);
        $referal = $this->getUserModel()->find((int) $request->post('referal'));
        if (is_null($referal) || $referal->referer_id != $user->getId()) {
            $this->createAlert(AdminAlert::MSG_TYPE_ERROR, 'Ошибка!', 'Игрок не найден, либо он не является рефералом выбранного игрока!')
                ->withBack(admin_url('core', 'players', 'player', ['user' => $user->name, 'server' => $server, 'tab' => 'referals']))
                ->render();
        }

        $referal->referer_id = null;
        $this->getUserModel()->update($referal);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Реферал был удален.')
            ->withBack(admin_url('core', 'players', 'player', ['user' => $user->name, 'server' => $server, 'tab' => 'referals']))
            ->withBack(admin_url('core', 'players', 'player', ['user' => $referal->name, 'server' => $server]), 'Вернуться к удаленному рефералу')
            ->render();
    }
}
