<?php


namespace App\Banlist\Events;


use App\Banlist\Entity\Ban;
use App\Core\Entity\User;

class UnbanEvent
{
    /**
     * @var User
     */
    public $admin;

    /**
     * @var Ban
     */
    public $ban;

    /**
     * UnbanEvent constructor.
     *
     * @param User $admin
     * @param Ban $ban
     */
    public function __construct(User $admin, Ban $ban)
    {
        $this->admin = $admin;
        $this->ban = $ban;
    }
}
