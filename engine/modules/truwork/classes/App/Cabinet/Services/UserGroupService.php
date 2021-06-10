<?php


namespace App\Cabinet\Services;


use App\Cabinet\Config;
use App\Cabinet\Entity\Group;
use App\Cabinet\Entity\User;
use App\Cabinet\Entity\UserGroup;
use App\Cabinet\Exceptions\GroupNotFoundException;
use App\Cabinet\Exceptions\PeriodExtendException;
use App\Core\Entity\Server;
use App\Core\Exceptions\Exception;
use App\Core\Support\Time;

class UserGroupService
{
    public const STATUS_ADD = 1;

    public const STATUS_EXTEND = 2;

    public const STATUS_REPLACE = 3;

    /**
     * @var Config
     */
    private $config;

    /**
     * UserGroupService constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $userGroups
     * @param bool $isPrimary
     * @return array
     */
    public function filterGroups(array $userGroups, bool $isPrimary): array
    {
        return array_filter($userGroups, function (UserGroup $userGroup) use ($isPrimary) {
            try {
                $group = $this->config->getGroup($userGroup->group_name);
            } catch (GroupNotFoundException $exception) {
                return false;
            }

            return $group->isPrimary() === $isPrimary;
        });
    }

    /**
     * @param User $user
     * @param Server $server
     * @param Group $group
     * @return UserGroup|null
     */
    public function getSimilarUserGroup(User $user, Server $server, Group $group): ?UserGroup
    {
        foreach ($user->getGroupManager()->getGroups() as $userGroup)
        {
            try {
                $group2 = $this->config->getGroup($userGroup->group_name);
            } catch (GroupNotFoundException $e) {
                continue;
            }
            if (is_null($group2)) continue;
            if ($userGroup->server_id == $server->getId() && ($group->isPrimary() == $group2->isPrimary() || $group->getName() == $userGroup->group_name)) {
                return $userGroup;
            }
        }

        return null;
    }

    /**
     * @param User $user
     * @param Group $group
     * @param Server $server
     * @param int $days
     * @return int
     * @throws Exception
     */
    public function giveGroup(User $user, Group $group, Server $server, int $days): int
    {
        $groupManager = $user->getGroupManager();

        $userGroup = $this->getSimilarUserGroup($user, $server, $group);

        if ($group->isPrimary()) {
            if (is_null($userGroup)) {
                $groupManager->addGroup($server, $group->getName(), $this->createExpiry($days));
                $groupManager->reloadGroups();
                return static::STATUS_ADD;
            } elseif ($userGroup->group_name == $group->getName()) {
                if($userGroup->expiry == 0) {
                    throw new PeriodExtendException($userGroup->group_name);
                }

                $userGroup->expiry = $days < 1 ? 0 : $userGroup->expiry + (86400 * $days);
                $groupManager->extendGroup($userGroup);
                $groupManager->reloadGroups();
                return static::STATUS_EXTEND;
            } else {
                $groupManager->replaceGroup(
                    $server,
                    array_keys($this->getGroups($group->isPrimary())), $group->getName(),
                    $this->createExpiry($days)
                );
                $groupManager->reloadGroups();
                return static::STATUS_REPLACE;
            }
        } else {
            if (is_null($userGroup)) {
                $groupManager->addGroup(
                    $server,
                    $group->getName(),
                    $this->createExpiry($days),
                    $group->getPermission()
                );
                $groupManager->reloadGroups();
                return static::STATUS_ADD;
            } else {
                if($userGroup->expiry == 0) {
                    throw new PeriodExtendException($userGroup->group_name);
                }

                $userGroup->expiry = $days < 1 ? 0 : $userGroup->expiry + (86400 * $days);
                $groupManager->extendGroup($userGroup);
                $groupManager->reloadGroups();
                return static::STATUS_EXTEND;
            }
        }
    }

    /**
     * @param User $user
     * @param Group $group
     * @param Server $server
     * @param int $expiry
     */
    public function setGroup(User $user, Group $group, Server $server, int $expiry): void
    {
        if ($group->isPrimary()) {
            $replacingGroups = array_map(function (Group $group) {
                return $group->getName();
            }, $this->config->getGroups());
            $user->getGroupManager()->replaceGroup($server, $replacingGroups, $group->getName(), $expiry);
        } else {
            $userGroup = $this->getSimilarUserGroup($user, $server, $group);
            if (!is_null($userGroup)) {
                $userGroup->expiry = $expiry;
                $user->getGroupManager()->extendGroup($userGroup);
            } else {
                $user->getGroupManager()->addGroup($server, $group->getName(), $expiry);
            }
        }
    }

    /**
     * @param bool $primary
     * @return array
     */
    private function getGroups(bool $primary): array
    {
        return array_filter($this->config->getGroupsArray(), function ($group) use ($primary) {
            return $group['is_primary'] === $primary;
        });
    }

    /**
     * @param int $days
     * @return int
     */
    private function createExpiry(int $days): int
    {
        if ($days < 1) {
            return 0;
        }

        return Time::now()->getTimestamp() + (86400 * $days);
    }
}
