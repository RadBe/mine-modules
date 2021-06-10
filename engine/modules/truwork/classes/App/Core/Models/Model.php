<?php


namespace App\Core\Models;


use App\Core\Application;
use App\Core\Database\Database;
use App\Core\Database\DB;
use App\Core\Database\QueryBuilder;
use App\Core\Pagination\PaginatedResult;

abstract class Model
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Database
     */
    protected $db;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string|null
     */
    protected $tablePrefix;

    /**
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * Model constructor.
     *
     * @param Application $app
     * @param string|null $connectionName
     */
    public function __construct(Application $app, ?string $connectionName = null)
    {
        $this->app = $app;
        $this->db = DB::getConnection($connectionName);
        static::boot($this);
    }

    /**
     * @param Model $model
     */
    protected static function boot(Model $model): void
    {
        //
    }

    /**
     * @param QueryBuilder $query
     * @param int $perPage
     * @return PaginatedResult
     */
    protected function paginated(QueryBuilder $query, int $perPage = 10): PaginatedResult
    {
        $totalQuery = clone $query;
        $totalQuery->select('count(*) as total');

        $paginatedResult = new PaginatedResult(
            $query,
            (int) $this->db->findOne($totalQuery)['total'],
            $perPage,
            $this->app->getRequest()->getPage()
        );

        $rows = $this->db->findAll($query);
        if (is_null($rows)) {
            $rows = [];
        }

        return $paginatedResult->setResult($rows);
    }

    /**
     * @return Database
     */
    public function getConnection(): Database
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return (empty($this->tablePrefix) ? '' : $this->tablePrefix . '_') . $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getIdColumn(): string
    {
        return $this->idColumn;
    }

    /**
     * @param string $idColumn
     */
    public function setIdColumn(string $idColumn): void
    {
        $this->idColumn = $idColumn;
    }

    /**
     * @param string $alias
     * @return QueryBuilder
     */
    public function createQuery(string $alias = 't'): QueryBuilder
    {
        return new QueryBuilder($this->getTable(), $alias);
    }

    /**
     * @param bool $paginated
     * @param int $perPage
     * @return PaginatedResult|array
     */
    public function getAll(bool $paginated = false, int $perPage = 10)
    {
        if ($paginated) {
            return $this->paginated($this->createQuery(), $perPage);
        }

        return is_null($rows = $this->db->findAll($this->createQuery()))
            ? []
            : $rows;
    }

    /**
     * @param string[]|int[] $ids
     * @return array
     */
    public function getAllByIds(array $ids): array
    {
        $ids = array_values(array_unique($ids));
        return $this->db->findAll(
            $this->createQuery()
                ->where($this->getIdColumn() . ' IN (' . QueryBuilder::getPlaceholdersIn($ids) . ')', ...$ids)
        );
    }

    /**
     * @param string|int $id
     * @return array|null
     */
    public function find($id)
    {
        return $this->db->findOne($this->createQuery()->where($this->getIdColumn() . ' = ?', $id));
    }

    /**
     * @param string $column
     * @param $value
     * @return array|null
     */
    public function findBy(string $column, $value)
    {
        return $this->db->findOne($this->createQuery()->where($column . ' = ?', $value));
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return array|object[]|null
     */
    public function findAllBy(string $column, $value): ?array
    {
        return $this->db->findAll($this->createQuery()->where($column . ' = ?', $value));
    }

    /**
     * @param string $column
     * @param $value
     * @return PaginatedResult
     */
    public function findAllPaginatedBy(string $column, $value): PaginatedResult
    {
        return $this->paginated($this->createQuery()->where($column . ' = ?', $value));
    }
}
