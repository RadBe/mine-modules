<?php


namespace App\Cabinet\Listeners;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Models\PaymentHistoryModel;
use App\Core\Application;
use App\Core\Events\Listener;
use App\TopVotes\Events\MonthResultEvent;

class VotesMonthResultListener implements Listener
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
     * @param MonthResultEvent $event
     */
    public function handle($event)
    {
        foreach ($event->result as $position => $data)
        {
            foreach ($data['rewards'] as $type => $amount)
            {
                if ($type == 'money') {
                    $this->historyModel->insert(PaymentHistory::createEntity(
                        $data['user']->entity(),
                        'место #' . ($position + 1),
                        $amount
                    ));
                }
            }
        }
    }
}
