<?php


namespace App\Cabinet\Events;


use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Events\LogEvent;
use App\Core\Support\Str;

class ExchangeMoneyEvent extends LogEvent
{
    /**
     * @var int
     */
    public $amount;

    /**
     * ExchangeMoneyEvent constructor.
     *
     * @param User $user
     * @param Server $server
     * @param int $cost
     * @param int $amount
     */
    public function __construct(User $user, Server $server, int $cost, int $amount)
    {
        $this->user = $user;
        $this->server = $server;
        $this->cost = $cost;
        $this->amount = $amount;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $rubWord = Str::declensionNumber($this->cost, 'рубля', 'рублей', 'рублей');
        $coinsWord = Str::declensionNumber($this->amount, 'монету', 'монеты', 'монет');

        return sprintf('Обмен %d %s на %d %s', $this->cost, $rubWord, $this->amount, $coinsWord);
    }
}
