<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Events\ExchangeMoneyEvent;
use App\Cabinet\Events\TransferMoneyEvent;
use App\Cabinet\Middleware\CheckModuleEnabled;
use App\Cabinet\Models\PaymentHistoryModel;
use App\Cabinet\Module;
use App\Core\Application;
use App\Core\Exceptions\Exception;
use App\Core\Exceptions\NotEnoughMoneyException;
use App\Core\Game\Money\GameMoneyManager;
use App\Core\Game\Money\Models\GameMoneyModel;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Http\Traits\NeedUser;
use Respect\Validation\Validator;

class BalanceController extends Controller
{
    use NeedUser, NeedServer;

    /**
     * @var GameMoneyManager
     */
    private $gameMoneyManager;

    /**
     * BalanceController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->middleware(new CheckModuleEnabled($module->getConfig()), 'balance_transfer')->only('transfer');
        $this->middleware(new CheckModuleEnabled($module->getConfig()), 'balance_exchange')->only('exchange');
        $this->gameMoneyManager = $app->make(GameMoneyManager::class, $module->getConfig());
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws NotEnoughMoneyException
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function transfer(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('amount', Validator::numericVal()->min(1))
        );

        $amount = (int) $request->post('amount');
        if (!$request->user()->hasMoney($amount)) {
            throw new NotEnoughMoneyException($amount);
        }

        $user = $this->getUser($request);
        if ($user->getId() === $request->user()->getId()) {
            throw new Exception('Нельзя передавать деньги самому себе!');
        }

        $request->user()->withdrawMoney($amount);
        $this->userModel->updateBalance($request->user());
        $user->depositMoney($amount);
        $this->userModel->updateBalance($user);

        $this->app->make(PaymentHistoryModel::class)->insert(
            PaymentHistory::createEntity($user, $request->user()->getName(), $amount)
        );
        dispatch(new TransferMoneyEvent($request->user(), $user, $amount));

        $this->printJsonResponse(
            true, 'Успех!', sprintf('Вы передали %d руб. игроку %s', $amount, $user->getName()), [
                'balance' => $request->user()->getMoney()
            ]
        );
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws NotEnoughMoneyException
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function exchange(Request $request)
    {
        $request->checkCsrf();
        $request->validate(Validator::key('amount', Validator::numericVal()->min(1)));

        $server = $this->getServer($request);

        $rate = $this->module->getConfig()->getGameMoneyRate();
        $price = (int) $request->post('amount');
        /* @var GameMoneyModel $gameMoneyModel */
        $gameMoneyModel = $this->gameMoneyManager->getGameMoneyModel($server);
        $gameMoney = $gameMoneyModel->findByUser($request->user());
        if (is_null($gameMoney)) {
            throw new Exception('Игровой аккаунт с балансом не найден!');
        }

        if (!$request->user()->hasMoney($price)) {
            throw new NotEnoughMoneyException($price);
        }
        $request->user()->withdrawMoney($price);
        $this->getUserModel()->updateBalance($request->user());

        $gameMoney->depositCoins($price * $rate);
        $gameMoneyModel->update($gameMoney);

        dispatch(new ExchangeMoneyEvent($request->user(), $server, $price, $price * $rate));

        $this->printJsonResponse(true, 'Успех!', sprintf('Вы успешно обменяли %d руб. на %d монет на сервере %s', $price, $price * $rate, $server->name), [
            'balance' => $request->user()->getMoney()
        ]);
    }
}
