<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Events\UnbanEvent;
use App\Cabinet\Middleware\CheckModuleEnabled;
use App\Cabinet\Module;
use App\Core\Application;
use App\Core\Exceptions\Exception;
use App\Core\Exceptions\ModuleNotFoundException;
use App\Core\Exceptions\NotEnoughMoneyException;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedUser;

class UnbanController extends Controller
{
    use NeedUser;

    /**
     * UnbanController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->middleware(new CheckModuleEnabled($module->getConfig()), 'unban');
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws NotEnoughMoneyException
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function unban(Request $request)
    {
        $request->checkCsrf();

        if (!$this->app->hasModule('banlist')) {
            throw new ModuleNotFoundException();
        }

        $price = $this->module->getConfig()->getUnbanPrice();
        if (!$request->user()->hasMoney($price)) {
            throw new NotEnoughMoneyException($price);
        }

        $request->user()->withdrawMoney($price);

        if (!is_null($ban = $this->app->getModule('banlist')->getUnbanService()->unban($request->user()))) {
            $this->getUserModel()->updateBalance($request->user());
            dispatch(new UnbanEvent($request->user(), $price));
            $this->printJsonResponse(true, 'Успех!', 'Вы успешно разбанились.', [
                'balance' => $request->user()->getMoney()
            ]);
            die;
        }

        throw new Exception('Не удалось разбанить аккаунт.');
    }
}
