<?php


namespace App\Cabinet\Services;


use App\Cabinet\Entity\UserGroup;
use App\Cabinet\Models\UserGroupsModel;
use App\Core\Application;
use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Game\Permissions\Permissions;
use App\Core\Game\Permissions\PermissionsManager;

class GroupManager
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var UserGroup[]
     */
    private $groups;

    /**
     * @var PermissionsManager
     */
    private $permissionsManager;

    /**
     * GroupManager constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->permissionsManager = Application::getInstance()->make(PermissionsManager::class);
    }

    /**
     * @param Server $server
     * @return Permissions
     */
    protected function getGamePermissions(Server $server): Permissions
    {
        return $this->permissionsManager->getPermissions($server);
    }

    /**
     * @return array
     */
    public function reloadGroups(): array
    {
        return $this->groups = UserGroupsModel::getInstance()->getGroups($this->user);
    }

    /**
     * @return UserGroup[]
     */
    public function getGroups(): array
    {
        return is_null($this->groups)
            ? $this->reloadGroups()
            : $this->groups;
    }

    /**
     * @param Server $server
     * @param string $group
     * @param int $expiry
     * @param string|null $permission
     */
    public function addGroup(Server $server, string $group, int $expiry = 0, ?string $permission = null): void
    {
        UserGroupsModel::getInstance()->insert(
            new UserGroup([
                'user_id' => $this->user->getId(),
                'group_name' => $group,
                'server_id' => $server->getId(),
                'expiry' => $expiry
            ])
        );

        if (!is_null($permission)) {
            $this->getGamePermissions($server)->setPermission($this->user, $permission);
        } else {
            $this->getGamePermissions($server)->addGroup($this->user, $group, $expiry);
        }
    }

    /**
     * @param Server $server
     * @param string $group
     * @param int $expiry
     * @param string|null $permission
     */
    public function setGroup(Server $server, string $group, int $expiry = 0, ?string $permission = null): void
    {
        UserGroupsModel::getInstance()->setGroup(
            new UserGroup([
                'user_id' => $this->user->getId(),
                'group_name' => $group,
                'server_id' => $server->getId(),
                'expiry' => $expiry
            ])
        );

        if (!is_null($permission)) {
            $this->getGamePermissions($server)->setPermission($this->user, $permission);
        } else {
            $this->getGamePermissions($server)->setGroup($this->user, $group, $expiry);
        }
    }

    /**
     * @param Server $server
     * @param array $groups
     * @param string $group
     * @param int $expiry
     */
    public function replaceGroup(Server $server, array $groups, string $group, int $expiry = 0): void
    {
        $groups[] = $group;
        UserGroupsModel::getInstance()->replaceGroup(
            new UserGroup([
                'user_id' => $this->user->getId(),
                'group_name' => $group,
                'server_id' => $server->getId(),
                'expiry' => $expiry
            ]), $groups
        );
        $this->getGamePermissions($server)->replaceGroup($this->user, $groups, $group, $expiry);
    }

    /**
     * @param Server $server
     * @param string $group
     * @param string|null $permission
     */
    public function removeGroup(Server $server, string $group, ?string $permission = null): void
    {
        UserGroupsModel::getInstance()->removeGroup($this->user, $server, $group);

        if (!is_null($permission)) {
            $this->getGamePermissions($server)->removePermission($this->user, $permission);
        } else {
            $this->getGamePermissions($server)->removeGroup($this->user, $group);
        }
    }

    /**
     * @param Server $server
     * @param array $permissions
     */
    public function removeGroups(Server $server, array $permissions = []): void
    {
        UserGroupsModel::getInstance()->removeGroups($this->user, $server);
        if (!empty($permissions)) {
            $this->getGamePermissions($server)->removePermissions($this->user, $permissions);
        } else {
            $this->getGamePermissions($server)->removeGroups($this->user);
        }
    }

    /**
     * @param UserGroup $userGroup
     */
    public function extendGroup(UserGroup $userGroup): void
    {
        UserGroupsModel::getInstance()->update($userGroup);
    }
}
