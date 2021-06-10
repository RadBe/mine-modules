<?php


namespace App\Cabinet\Listeners;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Models\PaymentHistoryModel;
use App\Core\Application;
use App\Core\Events\Listener;
use App\TopVotes\Events\VoteEvent;

class VoteListener implements Listener
{
    /**
     * @var PaymentHistoryModel
     */
    private $historyModel;

    /**
     * VoteListener constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->historyModel = $app->make(PaymentHistoryModel::class);
    }

    /**
     * @var VoteEvent $event
     */
    public function handle($event)
    {
        foreach ($event->top->getRewards() as $type => $amount)
        {
            if ($type == 'money') {
                $this->historyModel->insert(PaymentHistory::createEntity($event->user, $event->top->name(), $amount));
            }
        }
    }
}
