<?php


namespace App\Banlist\Models;


use App\Banlist\Entity\Ban;
use App\Core\Application;
use App\Core\Entity\User;
use App\Core\Models\EntityModel;

class BansModel extends EntityModel
{
    /**
     * @var \App\Banlist\Config
     */
    protected $config;

    /**
     * @inheritDoc
     */
    public function __construct(Application $app, ?string $connectionName = null)
    {
        $this->config = $app->getModule('banlist')->getConfig();
        $this->table = $this->config->getTable();

        parent::__construct($app, $connectionName);
    }

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return $this->config->getEntity();
    }

    /**
     * @return array
     */
    private function getWhere(): array
    {
        $result = [];

        $permanentFormat = $this->getEntityClass()->getPermanentFormat();
        $expiryColumn = $this->getEntityClass()->getExpiryColumn();
        $where = '(';
        if (is_null($permanentFormat)) {
            $where .= "$expiryColumn IS NULL";
        } else {
            $where .= "$expiryColumn = ?";
            $result[] = $permanentFormat;
        }

        $where .= ' OR ' . $expiryColumn . ' > ?)';
        $result[] = $this->getEntityClass()->getNowFormat();
        if ($this->getEntityClass()->hasActiveColumn()) {
            $where .= ' AND ' . $this->config->getActiveColumn() . ' = 1';
        }

        return array_merge([$where], $result);
    }

    /**
     * @param bool $paginated
     * @param int $perPage
     * @param string $user
     * @return \App\Core\Pagination\PaginatedResult|array|object[]
     */
    public function getAll(bool $paginated = false, int $perPage = 10, string $user = '')
    {
        if (!empty($user)) {
            $where = $this->getWhere();
            $whereSQL = $where[0];
            unset($where[0]);

            return $this->paginated(
                $this->createQuery()
                    ->where($this->config->getUserColumn() . ' LIKE ? AND ' . $whereSQL,
                        "$user%", ...$where
                    )
            );
        }

        return $this->paginated(
            $this->createQuery()
                ->where(...$this->getWhere())
                ->orderBy($this->getEntityClass()->getSortColumn(), 'DESC'),
            $perPage
        );
    }

    /**
     * @param User $user
     * @return Ban|null
     */
    public function findByUser(User $user): ?Ban
    {
        $where = $this->getWhere();
        $whereSQL = $where[0];
        unset($where[0]);
        $query = $this->createQuery()
            ->where(
                $this->config->getUserColumn() . ' = ? AND ' . $whereSQL,
                $user->getName(), ...$where
            );

        return is_null($data = $this->db->findOne($query)) ? null : $this->createEntity($data);
    }

    /**
     * @param Ban $ban
     * @return bool
     */
    public function unban(Ban $ban): bool
    {
        if (!$this->getEntityClass()->hasActiveColumn()) {
            return $this->db->delete(
                $this->createQuery()
                    ->where($ban->getUserColumn() . ' = ?', $ban->getUser())
            );
        }

        return $this->db->update(
            $this->createQuery()
                ->data($ban->getActiveColumn(), 0)
                ->where($ban->getUserColumn() . ' = ?', $ban->getUser())
                ->where($ban->getActiveColumn() . ' = ?', 1)
        );
    }
}
