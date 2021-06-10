<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Events\PaymentEvent;
use App\Cabinet\Middleware\CheckModuleEnabled;
use App\Cabinet\Models\PaymentHistoryModel;
use App\Cabinet\Module;
use App\Cabinet\Services\Payment\Payers\Pool;
use App\Core\Application;
use App\Core\Exceptions\Exception;
use App\Core\Http\Middleware\Auth;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedUser;
use Respect\Validation\Validator;

class PaymentController extends Controller
{
    use NeedUser;

    /**
     * @inheritdoc
     */
    protected $auth = false;

    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @inheritDoc
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->pool = $app->make(Pool::class);
        $this->middleware(Auth::class)->only('history');
        $this->middleware(new CheckModuleEnabled($module->getConfig()), 'balance');
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function pay(Request $request)
    {
        $request->validate(Validator::key('payer', Validator::stringType()), false);

        try {
            $payer = $this->pool->getOrFail($request->get('payer'))->init($request);
        } catch (Exception $exception) {
            abort($exception->getMessage());
        }

        if (!$payer->isEnabled()) {
            print $payer->errorResponse('Метод оплаты отключен.');
            die;
        }

        try {
            if (!$payer->validate()) {
                print $payer->errorResponse('Invalid sign');
                die;
            }
        } catch (Exception $exception) {
            print $payer->errorResponse($exception->getMessage());
            die;
        }

        $username = $payer->getUsername();
        $user = is_int($username) ? $this->getUserModel()->find($username) : $this->getUserModel()->findByName($username);
        if (is_null($user)) {
            print $payer->errorResponse('Пользователь не найден!');
            die;
        }

        $result = $payer->complete($user);

        $user->depositMoney($payer->getSum());
        $this->userModel->updateBalance($user);

        dispatch(new PaymentEvent($user, $payer->getSum(), $payer));

        print $payer->successResponse($result);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function redirectPayer(Request $request)
    {
        $request->validate(
            Validator::key('payer', Validator::stringType())
                ->key('username', Validator::stringType())
                ->key('sum', Validator::intVal()->min(1)),
            false
        );

        $payer = $this->pool->getOrFail(strtolower($request->get('payer')));

        if (!$payer->isEnabled()) {
            $this->error('Метод оплаты отключен.');
        }

        $user = $this->getUserModel()->findByName($request->get('username'));
        if (is_null($user)) {
            $this->error('Пользователь не найден!');
        }

        $url = $payer->paymentUrl($user, (int) $request->get('sum'));

        if (Application::$ajaxMode) {
            $this->printJsonResponse(true, '', '', [
                'url' => $url
            ]);
            die;
        }

        header('Location: ' . $url);
        die;
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function history(Request $request)
    {
        $request->checkCsrf();

        /* @var PaymentHistoryModel $historyModel */
        $historyModel = $this->app->make(PaymentHistoryModel::class);
        $result = $historyModel->search(10, $request->user()->getName());

        $this->printJsonData([
            'rows' => array_map(function (PaymentHistory $history) {
                return $history->toArray();
            }, $result->getResult()),
            'pagination' => $result->paginationData()
        ]);
        die;
    }
}
