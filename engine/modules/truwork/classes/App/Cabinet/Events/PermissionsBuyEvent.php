<?php


namespace App\Cabinet\Events;


use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Events\LogEvent;

class PermissionsBuyEvent extends LogEvent
{
    /**
     * @var string
     */
    public $perm;

    /**
     * PermissionsBuyEvent constructor.
     *
     * @param User $user
     * @param Server|null $server
     * @param int $cost
     * @param string $perm
     */
    public function __construct(User $user, ?Server $server, int $cost, string $perm)
    {
        $this->user = $user;
        $this->server = $server;
        $this->cost = $cost;
        $this->perm = $perm;
    }


    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return sprintf('Покупка права %s', $this->perm);
    }
}
