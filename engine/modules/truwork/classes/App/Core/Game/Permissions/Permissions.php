<?php


namespace App\Core\Game\Permissions;


use App\Core\Database\Database;
use App\Core\Entity\Server;
use App\Core\Entity\User;

interface Permissions
{
    /**
     * Permissions constructor.
     *
     * @param Database $db
     * @param Server $server
     */
    public function __construct(Database $db, Server $server);

    /**
     * @param array $groups
     * @return array
     */
    public function getUsersInGroups(array $groups = []): array;

    /**
     * @param User $user
     * @param string $group
     * @param int $expiry
     */
    public function addGroup(User $user, string $group, int $expiry = 0): void;

    /**
     * @param User $user
     * @param string $group
     * @param int $expiry
     */
    public function setGroup(User $user, string $group, int $expiry = 0): void;

    /**
     * @param User $user
     * @param array $groups
     * @param string $group
     * @param int $expiry
     */
    public function replaceGroup(User $user, array $groups, string $group, int $expiry = 0): void;

    /**
     * @param User $user
     * @param string $group
     */
    public function removeGroup(User $user, string $group): void;

    /**
     * @param User $user
     */
    public function removeGroups(User $user): void;

    /**
     * @param User $user
     * @param string $permission
     * @param string $value
     * @param int $expiry
     */
    public function setPermission(User $user, string $permission, string $value = '', int $expiry = 0): void;

    /**
     * @param User $user
     * @param string $permission
     */
    public function removePermission(User $user, string $permission): void;

    /**
     * @param User $user
     * @param array $permission
     */
    public function removePermissions(User $user, array $permissions): void;

    /**
     * @param User $user
     * @return PrefixSuffix
     */
    public function getPrefixSuffix(User $user): PrefixSuffix;

    /**
     * @param User $user
     * @param PrefixSuffix $prefixSuffix
     */
    public function setPrefixSuffix(User $user, PrefixSuffix $prefixSuffix): void;

    /**
     * @param User $user
     */
    public function removePrefixSuffix(User $user): void;
}
