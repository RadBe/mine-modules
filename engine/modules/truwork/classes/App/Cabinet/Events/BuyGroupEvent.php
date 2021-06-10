<?php


namespace App\Cabinet\Events;


use App\Cabinet\Entity\Group;
use App\Cabinet\Services\UserGroupService;
use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Events\LogEvent;
use App\Core\Support\Str;

class BuyGroupEvent extends LogEvent
{
    /**
     * @var Group
     */
    public $group;

    /**
     * @var int
     */
    public $period;

    /**
     * @var int
     */
    public $action;

    /**
     * BuyGroupEvent constructor.
     *
     * @param User $user
     * @param Server $server
     * @param Group $group
     * @param int $period
     * @param int $cost
     * @param int $action
     */
    public function __construct(User $user, Server $server, Group $group, int $period, int $cost, int $action)
    {
        $this->user = $user;
        $this->server = $server;
        $this->group = $group;
        $this->period = $period;
        $this->cost = $cost;
        $this->action = $action;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        switch ($this->action)
        {
            case UserGroupService::STATUS_EXTEND: $action = 'Продление группы'; break;
            case UserGroupService::STATUS_REPLACE: $action = 'Замена группы на'; break;
            default: $action = 'Покупка группы';
        }
        $daysWord = Str::declensionNumber($this->period, 'день', 'дня', 'дней');
        $period = $this->period < 1 ? 'навсегда' : 'на ' . $this->period . ' ' . $daysWord;

        return "$action {$this->group->getName()} $period";
    }
}
