<?php


namespace App\Cabinet\Events;


use App\Core\Entity\User;
use App\Core\Events\LogEvent;
use App\Core\Support\Str;

class TransferMoneyEvent extends LogEvent
{
    /**
     * @var User
     */
    public $target;

    /**
     * TransferMoneyEvent constructor.
     *
     * @param User $user
     * @param User $target
     * @param int $cost
     */
    public function __construct(User $user, User $target, int $cost)
    {
        $this->user = $user;
        $this->target = $target;
        $this->cost = $cost;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $rubWord = Str::declensionNumber($this->cost, 'рубля', 'рублей', 'рублей');

        return sprintf('Перевод %d %s игроку %s (id: %s)', $this->cost, $rubWord, $this->target->name, $this->target->getId());
    }
}
