<?php


namespace App\Cabinet\Listeners;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Models\PaymentHistoryModel;
use App\Core\Application;
use App\Core\Events\Listener;
use App\Promo\Events\PromoActivateEvent;

class PromoListener implements Listener
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * PromoListener constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param PromoActivateEvent $event
     * @return bool|void
     */
    public function handle($event)
    {
        $this->app->make(PaymentHistoryModel::class)->insert(
            PaymentHistory::createEntity($event->user, 'промо-код', $event->promo->amount)
        );
    }
}
