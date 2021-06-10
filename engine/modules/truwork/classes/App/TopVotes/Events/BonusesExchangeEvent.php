<?php


namespace App\TopVotes\Events;


use App\TopVotes\Entity\User;

class BonusesExchangeEvent
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var int
     */
    public $price;

    /**
     * @var int
     */
    public $amount;

    /**
     * BonusesExchangeEvent constructor.
     *
     * @param User $user
     * @param int $price
     * @param int $amount
     */
    public function __construct(User $user, int $price, int $amount)
    {
        $this->user = $user;
        $this->price = $price;
        $this->amount = $amount;
    }
}
