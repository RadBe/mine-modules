<?php


namespace App\Core\Game\Permissions;


use App\Core\Database\Database;
use App\Core\Database\QueryBuilder;
use App\Core\Entity\Server;
use App\Core\Entity\User;

class PermissionsEx implements Permissions
{
    /**
     * @var Database
     */
    protected $db;

    /**
     * @var string
     */
    protected $tableGroups = 'permissions_inheritance';

    /**
     * @var string
     */
    protected $tablePermissions = 'permissions';

    /**
     * @inheritDoc
     */
    public function __construct(Database $db, Server $server)
    {
        $this->db = $db;
    }

    /**
     * @param array $groups
     * @return array
     */
    public function getUsersInGroups(array $groups = []): array
    {
        $query = (new QueryBuilder($this->tableGroups, 't'))
            ->select('t.child, t.`parent`, p.`value`')
            ->join('INNER JOIN ' . $this->tablePermissions . ' p ON t.child = p.name');
        $where = [
            'sql' => 'p.permission = ? AND p.type = 1',
            'data' => ['name']
        ];
        if (!empty($groups)) {
            $where['sql'] .= ' AND t.parent IN (' . QueryBuilder::getPlaceholdersIn($groups) . ')';
            $where['data'] = array_merge($where['data'], $groups);
        }
        $query->where($where['sql'], ...$where['data']);

        return array_map(function (array $row) {
            return [
                'name' => $row['value'],
                'group' => $row['parent'],
                'uuid' => $row['child']
            ];
        }, $this->db->findAll($query));
    }

    /**
     * @inheritDoc
     */
    public function addGroup(User $user, string $group, int $expiry = 0): void
    {
        $this->db->insert(
            (new QueryBuilder($this->tableGroups))
                ->data('child', $user->getUUID())
                ->data('parent', $group)
                ->data('type', 1)
                ->data('world', null)
        );
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
        $this->db->delete(
            (new QueryBuilder($this->tableGroups))
                ->where(
                    'child = ? AND parent IN (' . QueryBuilder::getPlaceholdersIn($groups) . ')',
                    $user->getUUID(), ...$groups)
        );
        $this->addGroup($user, $group, $expiry);
    }

    /**
     * @inheritDoc
     */
    public function removeGroup(User $user, string $group): void
    {
        $this->db->delete(
            (new QueryBuilder($this->tableGroups))
                ->where('child = ? AND parent = ?', $user->getUUID(), $group)
        );
    }

    /**
     * @inheritDoc
     */
    public function removeGroups(User $user): void
    {
        $this->db->delete((new QueryBuilder($this->tableGroups))->where('child = ?', $user->getUUID()));
    }

    /**
     * @inheritDoc
     */
    public function setPermission(User $user, string $permission, string $value = '', int $expiry = 0): void
    {
        $this->removePermission($user, $permission);
        $this->db->insert(
            (new QueryBuilder($this->tablePermissions))
                ->data('name', $user->getUUID())
                ->data('permission', $permission)
                ->data('type', 1)
                ->data('world', '')
                ->data('value', $value)
        );
    }

    /**
     * @inheritDoc
     */
    public function removePermission(User $user, string $permission): void
    {
        $this->db->delete(
            (new QueryBuilder($this->tablePermissions))
                ->where('name = ? AND type = 1 AND permission = ?', $user->getUUID(), $permission)
        );
    }

    /**
     * @inheritDoc
     */
    public function removePermissions(User $user, array $permissions): void
    {
        $this->db->delete(
            (new QueryBuilder($this->tablePermissions))
                ->where(
                    'name = ? AND type = 1 AND permission IN (' . QueryBuilder::getPlaceholdersIn($permissions) . ')',
                    $user->getUUID(), ...$permissions
                )
        );
    }

    /**
     * @inheritDoc
     */
    public function getPrefixSuffix(User $user): PrefixSuffix
    {
        $permissions = $this->db->findAll(
            (new QueryBuilder($this->tablePermissions))
                ->where(
                    'name = ? AND (permission = ? OR permission = ?)',
                    $user->getUUID(), 'prefix', 'suffix')
                ->groupBy('permission')
                ->limit(2)
        );

        $prefix = $suffix = null;
        foreach ($permissions as $permission)
        {
            if ($permission['permission'] == 'prefix') {
                $prefix = $permission['value'];
            } else {
                $suffix = $permission['value'];
            }
        }

        if (empty($prefix) || empty($suffix)) {
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

        $this->db->insert(
            (new QueryBuilder($this->tablePermissions))
                ->data('name', $user->getUUID())
                ->data('permission', 'prefix')
                ->data('type', 1)
                ->data('world', '')
                ->data('value', $prefixSuffix->prefixToPermissionFormat())
        );

        $this->db->insert(
            (new QueryBuilder($this->tablePermissions))
                ->data('name', $user->getUUID())
                ->data('permission', 'suffix')
                ->data('type', 1)
                ->data('world', '')
                ->data('value', $prefixSuffix->suffixToPermissionFormat())
        );
    }

    /**
     * @inheritDoc
     */
    public function removePrefixSuffix(User $user): void
    {
        $this->removePermissions($user, ['prefix', 'suffix']);
    }
}
