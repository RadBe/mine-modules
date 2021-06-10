<?php


namespace App\Cabinet\Events;


use App\Cabinet\Services\Payment\Payers\Payer;
use App\Core\Entity\User;

class PaymentEvent
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var int
     */
    public $sum;

    /**
     * @var Payer
     */
    public $payer;

    /**
     * PaymentEvent constructor.
     *
     * @param User $user
     * @param int $sum
     * @param Payer $payer
     */
    public function __construct(User $user, int $sum, Payer $payer)
    {
        $this->user = $user;
        $this->sum = $sum;
        $this->payer = $payer;
    }
}
