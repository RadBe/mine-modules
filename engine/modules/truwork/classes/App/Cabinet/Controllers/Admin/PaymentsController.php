<?php


namespace App\Cabinet\Controllers\Admin;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Models\PaymentHistoryModel;
use App\Cabinet\Module;
use App\Cabinet\Services\Payment\Payers\Pool;
use App\Core\Application;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedUser;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class PaymentsController extends AdminController
{
    use NeedUser;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * PaymentsController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws \App\Core\Exceptions\Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->pool = $app->make(Pool::class);
    }

    /**
     * @return void
     */
    public function index()
    {
        $this->createView($this->module->getName())
            ->addBreadcrumb($this->module->getName(), admin_url('cabinet'))
            ->addBreadcrumb('Управление платежами')
            ->render('cabinet/payments/index');
    }

    /**
     * @param Request $request
     */
    public function settings(Request $request)
    {
        $payers = $this->pool->all();

        $this->createView('Настройки платежей')
            ->addBreadcrumb($this->module->getName(), admin_url('cabinet'))
            ->addBreadcrumb('Управление платежами', admin_url('cabinet', 'payments'))
            ->addBreadcrumb('Настройки платежей')
            ->render('cabinet/payments/settings', [
                'tab' => $request->get('tab', optional($payers[0])->name()),
                'payers' => $payers
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Cabinet\Exceptions\PayerNotFoundException
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveUnitpay(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('enabled', Validator::boolVal(), false)
                ->key('public_key', Validator::stringType())
                ->key('secret_key', Validator::stringType())
        );

        $payer = $this->pool->getOrFail('unitpay');
        $payer->setIsEnabled((bool) $request->post('enabled', false));
        $payer->setPublicKey($request->post('public_key'));
        $payer->setSecretKey($request->post('secret_key'));

        $this->module->getConfig()->updatePayer($payer);
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки Unitpay сохранены.')
            ->withBack(admin_url('cabinet', 'payments', 'settings', ['tab' => $payer->name()]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Cabinet\Exceptions\PayerNotFoundException
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveQiwi(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('enabled', Validator::boolVal(), false)
                ->key('public_key', Validator::stringType())
                ->key('secret_key', Validator::stringType())
                ->key('theme', Validator::stringType(), false)
                ->key('comment', Validator::stringType())
                ->key('success_url', Validator::stringType())
        );

        $payer = $this->pool->getOrFail('qiwi');
        $payer->setIsEnabled((bool) $request->post('enabled', false));
        $payer->setPublicKey(htmlspecialchars($request->post('public_key')));
        $payer->setSecretKey(htmlspecialchars($request->post('secret_key')));
        $payer->setTheme(htmlspecialchars($request->post('theme', '')));
        $payer->setComment(htmlspecialchars($request->post('comment')));
        $payer->setSuccessUrl(htmlspecialchars($request->post('success_url', '')));

        $this->module->getConfig()->updatePayer($payer);
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки Qiwi сохранены.')
            ->withBack(admin_url('cabinet', 'payments', 'settings', ['tab' => $payer->name()]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Cabinet\Exceptions\PayerNotFoundException
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveFreekassa(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('enabled', Validator::boolVal(), false)
                ->key('id', Validator::intVal())
                ->key('secret_key1', Validator::stringType())
                ->key('secret_key2', Validator::stringType())
        );

        $payer = $this->pool->getOrFail('freekassa');
        $payer->setIsEnabled((bool) $request->post('enabled', false));
        $payer->setId((int) $request->post('id'));
        $payer->setSecretKey1($request->post('secret_key1'));
        $payer->setSecretKey2($request->post('secret_key2'));

        $this->module->getConfig()->updatePayer($payer);
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки Freekassa сохранены.')
            ->withBack(admin_url('cabinet', 'payments', 'settings', ['tab' => $payer->name()]))
            ->render();
    }

    /**
     * @param Request $request
     */
    public function payments(Request $request)
    {
        $searchUser = $request->any('username');

        $this->createView('История платежей')
            ->addBreadcrumb($this->module->getName(), admin_url('cabinet'))
            ->addBreadcrumb('Управление платежами', admin_url('cabinet', 'payments'))
            ->addBreadcrumb('История платежей')
            ->render('cabinet/payments/payments', [
                'payments' => $this->getPaymentHistoryModel()->search(50, $searchUser),
                'searchUser' => htmlspecialchars($searchUser)
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\Exception
     */
    public function create(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('sum', Validator::intVal()->min(1))
                ->key('deposit', Validator::boolVal(), false)
        );

        $deposit = (bool) $request->post('deposit', false);

        $user = $this->getUser($request);
        $sum = (int) $request->post('sum');

        if ($deposit) {
            $user->depositMoney($sum);
            $this->userModel->updateBalance($user);
        }

        $this->getPaymentHistoryModel()->insert(
            PaymentHistory::createEntity($user, $request->user()->getName(), $sum)
        );

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Платеж добавлен.')
            ->withBack(admin_url('cabinet', 'payments', 'payments'))
            ->render();
    }

    /**
     * @return PaymentHistoryModel
     */
    private function getPaymentHistoryModel(): PaymentHistoryModel
    {
        return $this->app->make(PaymentHistoryModel::class);
    }
}
