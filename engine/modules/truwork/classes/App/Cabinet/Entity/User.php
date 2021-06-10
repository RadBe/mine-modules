<?php


namespace App\Cabinet\Entity;


use App\Cabinet\Services\GroupManager;
use App\Core\Entity\Server;
use App\Core\Entity\User as BaseUser;

class User
{
    /**
     * @var BaseUser
     */
    protected $user;

    /**
     * @var GroupManager
     */
    private $groupManager;

    /**
     * User constructor.
     *
     * @param BaseUser $user
     */
    public function __construct(BaseUser $user)
    {
        $this->user = $user;
    }

    /**
     * @return BaseUser
     */
    public function getUser(): BaseUser
    {
        return $this->user;
    }

    /**
     * @return GroupManager
     */
    public function getGroupManager(): GroupManager
    {
        if (is_null($this->groupManager)) {
            $this->groupManager = new GroupManager($this->user);
        }

        return $this->groupManager;
    }

    /**
     * @param Server|null $server
     * @param string|array $groups
     * @return bool
     */
    public function inGroups(?Server $server, $groups): bool
    {
        if (!is_array($groups)) {
            $groups = [$groups];
        }

        if (in_array('default', $groups)) {
            return true;
        }

        foreach ($this->getGroupManager()->getGroups() as $userGroup)
        {
            if ((is_null($server) || $server->getId() == $userGroup->server_id) && in_array($userGroup->group_name, $groups)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Server|null $server
     * @param string $permission
     * @param array|string $groups
     * @return bool
     */
    public function hasPermissionOrGroups(?Server $server, string $permission, $groups): bool
    {
        return $this->user->hasPermission($server, $permission) || $this->inGroups($server, $groups);
    }

    /**
     * @param BaseUser $user
     * @return static
     */
    public static function swap(BaseUser $user)
    {
        return new static($user);
    }
}
