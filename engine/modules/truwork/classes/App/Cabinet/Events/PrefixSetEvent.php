<?php


namespace App\Cabinet\Events;


use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Events\LogEvent;
use App\Core\Game\Permissions\PrefixSuffix;

class PrefixSetEvent extends LogEvent
{
    /**
     * @var PrefixSuffix
     */
    public $prefix;

    /**
     * PrefixSetEvent constructor.
     *
     * @param User $user
     * @param Server $server
     * @param PrefixSuffix $prefix
     */
    public function __construct(User $user, Server $server, PrefixSuffix $prefix)
    {
        $this->user = $user;
        $this->server = $server;
        $this->prefix = $prefix;
    }


    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return sprintf('Изменение префикса на %s', $this->prefix->prefixToPermissionFormat() . ' ' . $this->prefix->suffixToPermissionFormat());
    }
}
