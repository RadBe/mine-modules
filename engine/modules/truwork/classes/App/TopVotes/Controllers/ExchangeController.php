<?php


namespace App\TopVotes\Controllers;


use App\Core\Application;
use App\Core\Entity\Module;
use App\Core\Entity\Server;
use App\Core\Exceptions\Exception;
use App\Core\Exceptions\NotEnoughMoneyException;
use App\Core\Game\Money\GameMoneyManager;
use App\Core\Game\Money\Models\GameMoneyModel;
use App\Core\Http\Controller;
use App\Core\Http\Middleware\Auth;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Support\Str;
use App\Core\View\Alert;
use App\TopVotes\Entity\User;
use App\TopVotes\Events\BonusesExchangeEvent;
use App\TopVotes\Models\UserModel;
use Respect\Validation\Validator;

class ExchangeController extends Controller
{
    use NeedServer;

    /**
     * @inheritDoc
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->middleware(Auth::class);
    }

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $user = User::swap($request->user());

        $view = $this->createView('top-votes/exchange.tpl', [
            'bonuses' => $user->getBonuses(),
            'bonuses-word' => Str::declensionNumber($user->getBonuses(), 'бонус', 'бонуса', 'бонусов'),
            'rate' => $this->module->getConfig()->getBonusesGameMoneyRate(),
            'servers' => implode('', array_map(function (Server $server) {
                return "<option value='{$server->id}'>{$server->name} {$server->version}</option>";
            }, $this->getServersModel()->getEnabled())),
            'csrf' => tw_csrf(true),
        ])
            ->if('bootstrap', $this->app->getConfig()->useBootstrap());

        if (!empty($request->post('amount'))) {
            try {
                $message = $this->doExchange($request);
                $view->setDataValue('bonuses', $user->getBonuses());
                $view->setDataValue('bonuses-word', Str::declensionNumber($user->getBonuses(), 'бонус', 'бонуса', 'бонусов'));
                $view->addAlert(new Alert(true, $message));
            } catch (Exception $exception) {
                $view->addAlert(new Alert(false, $exception->getMessage()));
            }
        }

        $view->compile();
    }

    /**
     * @param Request $request
     * @return string
     * @throws Exception
     */
    protected function doExchange(Request $request): string
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('server', Validator::numericVal())
                ->key('amount', Validator::numericVal()->min(1))
        );

        $price = (int) $request->post('amount');
        $server = $this->getServer($request);
        $rate = $this->module->getConfig()->getBonusesGameMoneyRate();
        $amount = $price * $rate;
        /* @var GameMoneyModel $gameMoneyModel */
        $gameMoneyModel = $this->app->make(GameMoneyManager::class)->getGameMoneyModel($server);
        $gameMoney = $gameMoneyModel->findByUser($request->user());
        if (is_null($gameMoney)) {
            throw new Exception('Игровой аккаунт с балансом не найден!');
        }

        $user = User::swap($request->user());
        if (!$user->hasBonuses($price)) {
            throw new NotEnoughMoneyException($price);
        }
        $user->withdrawBonuses($price);

        /* @var UserModel $model */
        $model = $this->app->make(UserModel::class);
        $model->updateBonusesBalance($user);

        $gameMoney->depositCoins($amount);
        $gameMoneyModel->update($gameMoney);

        dispatch(new BonusesExchangeEvent($user, $price, $amount));

        return sprintf(
            'Вы успешно обменяли %d %s на %d %s на сервере %s.',
            $price,
            Str::declensionNumber($price, 'бонус', 'бонуса', 'бонусов'),
            $amount,
            Str::declensionNumber($amount, 'монету', 'монеты', 'монет'),
            $server->name
        );
    }
}
