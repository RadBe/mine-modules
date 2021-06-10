<?php


namespace App\Core\Game\Permissions;


use App\Core\Database\Database;
use App\Core\Database\QueryBuilder;
use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Support\Str;

class LuckPerms implements Permissions
{
    /**
     * @var Database
     */
    protected $db;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $tableUserPermissions = 'luckperms_user_permissions';

    /**
     * @var string
     */
    protected $tablePlayers = 'luckperms_players';

    /**
     * @inheritDoc
     */
    public function __construct(Database $db, Server $server)
    {
        $this->db = $db;
        $this->server = $server;
    }

    /**
     * @param array $groups
     * @return array
     */
    public function getUsersInGroups(array $groups = []): array
    {
        $query = (new QueryBuilder($this->tableUserPermissions, 't'))
            ->select('t.uuid, t.`permission`, p.`username`')
            ->join('INNER JOIN ' . $this->tablePlayers . ' p ON t.uuid = p.uuid');
        $where = [
            'sql' => 't.permission LIKE ?',
            'data' => ['group.%']
        ];
        if (!empty($groups)) {
            $where['sql'] = 't.permission IN (' . QueryBuilder::getPlaceholdersIn($groups) . ')';
            $where['data'] = array_map(function (string $group) {
                return "group.$group";
            }, $groups);
        }
        $where['sql'] .= ' AND t.server = ?';
        $where['data'][] = $this->getServerName();

        $query->where($where['sql'], ...$where['data']);

        return array_map(function (array $row) {
            return [
                'name' => $row['username'],
                'group' => Str::replaceFirst('group.', '', $row['permission']),
                'uuid' => $row['uuid']
            ];
        }, $this->db->findAll($query));
    }

    /**
     * @inheritDoc
     */
    public function addGroup(User $user, string $group, int $expiry = 0): void
    {
        $this->addPermission($user, "group.$group", $expiry);
    }

    /**
     * @inheritDoc
     */
    public function setGroup(User $user, string $group, int $expiry = 0): void
    {
        $this->removeGroups($user);
        $this->addGroup($user, $group, $expiry);
    }

    /**
     * @inheritDoc
     */
    public function replaceGroup(User $user, array $groups, string $group, int $expiry = 0): void
    {
        $groups = array_map(function (string $group) {
            return "group.$group";
        }, $groups);

        $this->db->delete(
            (new QueryBuilder($this->tableUserPermissions))
                ->where(
                    'uuid = ? AND server = ? AND permission IN (' . QueryBuilder::getPlaceholdersIn($groups) . ')',
                    $user->getUUID(), $this->getServerName(), ...$groups
                )
        );
        $this->addGroup($user, $group, $expiry);
    }

    /**
     * @inheritDoc
     */
    public function removeGroup(User $user, string $group): void
    {
        $this->removePermission($user, "group.$group");
    }

    /**
     * @inheritDoc
     */
    public function removeGroups(User $user): void
    {
        $this->db->delete(
            (new QueryBuilder($this->tableUserPermissions))
                ->where('uuid = ? AND server = ? AND permission LIKE \'group.%\'', $user->getUUID(), $this->getServerName())
        );
    }

    /**
     * @param User $user
     * @param string $permission
     * @param int $expiry
     */
    public function addPermission(User $user, string $permission, int $expiry = 0): void
    {
        $this->db->insert(
            (new QueryBuilder($this->tableUserPermissions))
                ->data('uuid', $user->getUUID())
                ->data('permission', $permission)
                ->data('value', 1)
                ->data('server', $this->getServerName())
                ->data('world', 'global')
                ->data('expiry', $expiry)
                ->data('contexts', '{}')
        );
    }

    /**
     * @inheritDoc
     */
    public function setPermission(User $user, string $permission, string $value = '', int $expiry = 0): void
    {
        $this->removePermission($user, $permission);
        $this->addPermission($user, $permission, $expiry);
    }

    /**
     * @inheritDoc
     */
    public function removePermission(User $user, string $permission): void
    {
        $this->db->delete(
            (new QueryBuilder($this->tableUserPermissions))
                ->where('uuid = ? AND server = ? AND permission = ?', $user->getUUID(), $this->getServerName(), $permission)
        );
    }

    /**
     * @inheritDoc
     */
    public function removePermissions(User $user, array $permissions): void
    {
        $this->db->delete(
            (new QueryBuilder($this->tableUserPermissions))
                ->where(
                    'uuid = ? AND server = ? AND permission IN (' . QueryBuilder::getPlaceholdersIn($permissions) . ')',
                    $user->getUUID(), $this->getServerName(), ...$permissions
                )
        );
    }

    /**
     * @inheritDoc
     */
    public function getPrefixSuffix(User $user): PrefixSuffix
    {
        $rows = $this->db->findAll(
            (new QueryBuilder($this->tableUserPermissions))
                ->where(
                    'uuid = ? AND server = ? AND (permission LIKE \'prefix.%\' OR permission LIKE \'suffix.%\')',
                    $user->getUUID(), $this->getServerName()
                )
        );

        $prefix = $suffix = null;
        foreach ($rows as $row)
        {
            [$perm, , $value] = explode('.', $row['permission'], 3);
            if ($perm == 'prefix') {
                $prefix = $value;
            } else {
                $suffix = $value;
            }
        }

        if (is_null($prefix) || is_null($suffix)) {
            return PrefixSuffix::createEmpty();
        }

        return PrefixSuffix::createFromPermission($prefix, $suffix);
    }

    /**
     * @inheritDoc
     */
    public function setPrefixSuffix(User $user, PrefixSuffix $prefixSuffix): void
    {
        $this->removePrefixSuffix($user);
        $this->addPermission($user, 'prefix.1.' . $prefixSuffix->prefixToPermissionFormat());
        $this->addPermission($user, 'suffix.1.' . $prefixSuffix->suffixToPermissionFormat());
    }

    /**
     * @inheritDoc
     */
    public function removePrefixSuffix(User $user): void
    {
        $this->db->delete(
            (new QueryBuilder($this->tableUserPermissions))
                ->where(
                    'uuid = ? AND server = ? AND (permission LIKE \'prefix.%\' OR permission LIKE \'suffix.%\')',
                    $user->getUUID(), $this->getServerName()
                )
        );
    }

    /**
     * @return string
     */
    public function getServerName(): string
    {
        return 'server_' . $this->server->id;
    }
}
