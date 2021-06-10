<?php


namespace App\Core\Models;


use App\Core\Database\QueryBuilder;
use App\Core\Entity\DatabaseEntity;
use App\Core\Pagination\PaginatedResult;

abstract class EntityModel extends Model
{
    /**
     * @inheritDoc
     */
    protected static function boot(Model $model): void
    {
        $model->setIdColumn($model->getEntityClass()::ID_COLUMN);
    }

    /**
     * @return object|string|null
     */
    abstract public function getEntityClass();

    /**
     * @param array $row
     * @return DatabaseEntity
     */
    protected function createEntity(array $row): DatabaseEntity
    {
        $entity = $this->getEntityClass();
        return new $entity($row);
    }

    /**
     * @param $rows
     * @return array
     */
    protected function createEntities($rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        return array_map(function ($row) {
            return $this->createEntity($row);
        }, $rows);
    }

    /**
     * @inheritDoc
     */
    protected function paginated(QueryBuilder $query, int $perPage = 10): PaginatedResult
    {
        $result = parent::paginated($query, $perPage);
        return $result->setResult($this->createEntities($result->getResult()));
    }

    /**
     * @inheritDoc
     */
    public function getAll(bool $paginated = false, int $perPage = 10)
    {
        $rows = parent::getAll($paginated, $perPage);
        if ($paginated) return $rows;

        return $this->createEntities($rows);
    }

    /**
     * @inheritDoc
     */
    public function getAllByIds(array $ids): array
    {
        return $this->createEntities(parent::getAllByIds($ids));
    }

    /**
     * @param $id
     * @return DatabaseEntity|null
     */
    public function find($id)
    {
        $row = parent::find($id);
        return is_null($row) ? null : $this->createEntity($row);
    }

    /**
     * @param string $column
     * @param $value
     * @return DatabaseEntity|null
     */
    public function findBy(string $column, $value)
    {
        $row = parent::findBy($column, $value);
        return is_null($row) ? null : $this->createEntity($row);
    }

    /**
     * @param string $column
     * @param $value
     * @return  DatabaseEntity[]|null
     */
    public function findAllBy(string $column, $value): ?array
    {
        $rows = parent::findAllBy($column, $value);
        return is_null($rows) ? null : $this->createEntities($rows);
    }

    /**
     * @param DatabaseEntity $entity
     * @return bool|int|string
     */
    public function insert(DatabaseEntity $entity)
    {
        $query = $this->createQuery();
        foreach ($entity->getAttributes() as $attribute => $value)
        {
            $query->data($attribute, $value);
        }

        $id = $this->db->insert($query);
        if ($entity::AUTOINCREMENT) {
            $entity->setId($id);
        }

        return $id;
    }

    /**
     * @param DatabaseEntity $entity
     * @return bool
     */
    public function update(DatabaseEntity $entity): bool
    {
        $query = $this->createQuery();
        foreach ($entity->getAttributes() as $attribute => $value)
        {
            $query->data($attribute, $value);
        }

        $query->where($this->getIdColumn() . ' = ?', $entity->getId());

        return $this->db->update($query);
    }

    /**
     * @param DatabaseEntity $entity
     * @return bool
     */
    public function delete(DatabaseEntity $entity): bool
    {
        $query = $this->createQuery()->where($this->getIdColumn() . ' = ?', $entity->getId());

        return $this->db->delete($query);
    }
}
