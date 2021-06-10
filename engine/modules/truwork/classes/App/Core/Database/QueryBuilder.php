<?php


namespace App\Core\Database;


class QueryBuilder
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $tableAlias;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $customData = [];

    /**
     * @var string
     */
    private $where;

    /**
     * @var array
     */
    private $groupBy = [];

    /**
     * @var array
     */
    private $selects = [];

    /**
     * @var array
     */
    private $joins = [];

    /**
     * @var array
     */
    private $orderBy = [];

    /**
     * @var array
     */
    private $bindings = [];

    /**
     * @var int
     */
    private $limit = 0;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * QueryBuilder constructor.
     *
     * @param string $table
     * @param string $alias
     */
    public function __construct(string $table, string $alias = '')
    {
        $this->table = $table;
        $this->tableAlias = $alias;
    }

    /**
     * @param $value
     */
    private function addToBindings($value): void
    {
        if (is_array($value)) {
            $this->bindings = array_merge($this->bindings, array_values($value));
        } else {
            $this->bindings[] = $value;
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * @return string|null
     */
    public function getWhere(): ?string
    {
        return $this->where;
    }

    /**
     * @return array
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @return array
     */
    public function getSelects(): array
    {
        return $this->selects;
    }

    /**
     * @return array
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param string ...$columns
     * @return $this
     */
    public function select(string ...$columns): self
    {
        $this->selects = $columns;

        return $this;
    }

    /**
     * @param string $join
     * @return $this
     */
    public function join(string $join): self
    {
        $this->joins[] = $join;

        return $this;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = $limit;

        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $column
     * @param string $sort
     * @return $this
     */
    public function orderBy(string $column, string $sort = 'ASC'): self
    {
        $this->orderBy = [$column, $sort];

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function data(string $key, $value): self
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif (is_array($value)) {
            $value = json_encode($value);
        }

        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);

            $this->addToBindings($key);
        } else {
            $this->data[$key] = $value;

            $this->addToBindings($value);
        }

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function customData($key): self
    {
        $this->customData[] = $key;

        return $this;
    }

    /**
     * @param string $where
     * @param mixed ...$params
     * @return $this
     */
    public function where(string $where, ...$params): self
    {
        $this->where = $where;
        $this->addToBindings($params);

        return $this;
    }

    /**
     * @param string ...$selects
     * @return $this
     */
    public function groupBy(string ...$selects): self
    {
        $this->groupBy = $selects;

        return $this;
    }

    /**
     * @param array $data
     * @return string
     */
    public static function getPlaceholdersIn(array $data): string
    {
        return implode(',', array_fill(0, count($data), '?'));
    }
}
