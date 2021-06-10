<?php


namespace App\Cabinet\Events;


use App\Cabinet\Entity\UserGroup;

class GroupsDeleteEvent
{
    /**
     * @var UserGroup[]
     */
    public $userGroups;

    /**
     * GroupsDeleteEvent constructor.
     *
     * @param UserGroup[] $userGroups
     */
    public function __construct(array $userGroups)
    {
        $this->userGroups = $userGroups;
    }
}
