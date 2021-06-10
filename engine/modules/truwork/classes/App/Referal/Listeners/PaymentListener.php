<?php


namespace App\Referal\Listeners;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Events\PaymentEvent;
use App\Cabinet\Models\PaymentHistoryModel;
use App\Core\Application;
use App\Core\Entity\User;
use App\Core\Events\Listener;
use App\Core\Http\Traits\NeedUser;
use App\Referal\Module;

class PaymentListener implements Listener
{
    use NeedUser;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Module
     */
    protected $module;

    /**
     * @var PaymentHistoryModel
     */
    protected $paymentHistoryModel;

    /**
     * PaymentListener constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->module = $app->getModule('referal');
        $this->paymentHistoryModel = $app->make(PaymentHistoryModel::class);
    }

    /**
     * @var User $user
     * @return User|null
     */
    protected function getReferer(User $user): ?User
    {
        return is_null($user->referer_id) ? null : $this->getUserModel()->find($user->referer_id);
    }

    /**
     * @param int $baseSum
     * @return int
     */
    protected function getSum(int $baseSum): int
    {
        $rate = $this->module->getConfig()->getReferalRate();
        if ($rate > 0) {
            return floor($baseSum - ($baseSum * ($rate / 100)));
        }

        return 0;
    }

    /**
     * @param User $referer
     * @param int $sum
     */
    protected function depositRefererSum(User $referer, int $sum): void
    {
        $referer->depositMoney($sum);
        $this->getUserModel()->update($referer);
    }

    /**
     * @param User $user
     * @param int $sum
     */
    protected function updateReferal(User $user, int $sum): void
    {
        $user->addRefererBal($sum);
        $this->getUserModel()->updateRefererBalance($user);
    }

    /**
     * @param PaymentEvent $event
     * @return bool|void
     */
    public function handle($event)
    {
        $referer = $this->getReferer($event->user);
        if (!is_null($referer) && ($sum = $this->getSum($event->sum)) > 0) {
            $this->depositRefererSum($referer, $sum);
            $this->updateReferal($event->user, $sum);
            $this->paymentHistoryModel->insert(PaymentHistory::createEntity($referer, 'Реферал', $sum));
        }
    }
}
