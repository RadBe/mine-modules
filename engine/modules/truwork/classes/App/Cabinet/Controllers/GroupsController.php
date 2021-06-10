<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Entity\UserGroup;
use App\Cabinet\Events\BuyGroupEvent;
use App\Cabinet\Middleware\CheckModuleEnabled;
use App\Cabinet\Module;
use App\Cabinet\Services\UserGroupService;
use App\Core\Application;
use App\Core\Entity\User;
use App\Core\Exceptions\NotEnoughMoneyException;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Http\Traits\NeedUser;
use Respect\Validation\Validator;

class GroupsController extends Controller
{
    use NeedServer, NeedUser;

    /**
     * GroupsController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws \App\Core\Exceptions\Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->middleware(new CheckModuleEnabled($module->getConfig()), 'groups');
    }

    /**
     * @param Request $request
     * @throws NotEnoughMoneyException
     * @throws \App\Core\Exceptions\Exception
     */
    public function buy(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('group', Validator::stringType())
                ->key('period', Validator::numericVal())
        );

        $period = (int) $request->post('period');
        $server = $this->getServer($request);
        $group = $this->module->getConfig()->getGroup($request->post('group'));
        $price = $group->getPrice($period);

        if (!$request->user()->hasMoney($price)) {
            throw new NotEnoughMoneyException($price);
        }

        $user = $this->user();

        /* @var UserGroupService $userGroupService */
        $userGroupService = $this->app->make(UserGroupService::class, $this->module->getConfig());
        $result = $userGroupService->giveGroup($user, $group, $server, $period);
        if ($result == UserGroupService::STATUS_ADD) {
            $message = 'Вы купили группу ' . strtoupper($group->getName());
        } elseif ($result == UserGroupService::STATUS_EXTEND) {
            $message = 'Вы продлили группу ' . strtoupper($group->getName());
        } else {
            $message = 'Вы заменили группу на ' . strtoupper($group->getName());
        }
        $this->withdrawMoney($request->user(), $price);

        dispatch(new BuyGroupEvent($user->getUser(), $server, $group, $period, $price, $result));

        $this->printJsonResponse(true, 'Успех!', $message, [
            'balance' => $request->user()->getMoney(),
            'groups' => array_map(function (UserGroup $userGroup) {
                return $userGroup->toArray();
            }, $user->getGroupManager()->getGroups())
        ]);
    }

    /**
     * @param User $user
     * @param int $price
     */
    private function withdrawMoney(User $user, int $price): void
    {
        $user->withdrawMoney($price);
        $this->getUserModel()->updateBalance($user);
    }
}
