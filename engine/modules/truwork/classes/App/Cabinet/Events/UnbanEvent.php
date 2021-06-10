<?php


namespace App\Cabinet\Events;


use App\Core\Entity\User;
use App\Core\Events\LogEvent;

class UnbanEvent extends LogEvent
{
    /**
     * UnbanEvent constructor.
     *
     * @param User $user
     * @param int $cost
     */
    public function __construct(User $user, int $cost)
    {
        $this->user = $user;
        $this->cost = $cost;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return 'Разбан';
    }
}
