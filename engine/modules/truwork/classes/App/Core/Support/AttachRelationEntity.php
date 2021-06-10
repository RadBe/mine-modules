<?php


namespace App\Core\Support;


use App\Core\Entity\DatabaseEntity;
use App\Core\Exceptions\Exception;
use App\Core\Models\Model;

class AttachRelationEntity
{
    /**
     * AttachRelationEntity constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param DatabaseEntity[] $entities
     * @param string $foreignKey
     * @return array
     */
    protected static function getForeignIds(array $entities, string $foreignKey): array
    {
        return array_map(function (object $entity) use ($foreignKey) {
            return $entity->{$foreignKey};
        }, $entities);
    }

    /**
     * @param DatabaseEntity[] $entities
     * @param Model $model
     * @param string $foreignKey
     * @throws Exception
     */
    public static function make(array $entities, Model $model, string $foreignKey): void
    {
        if (empty($entities)) return;

        $keys = static::getForeignIds($entities, $foreignKey);

        $foreignEntities = [];
        foreach ($model->getAllByIds($keys) as $entity)
        {
            $foreignEntities[$entity->getId()] = $entity;
        }

        foreach ($entities as $entity)
        {
            if (is_null($entity->{$foreignKey})) continue;

            if (!isset($foreignEntities[$entity->{$foreignKey}])) {
                throw new Exception('Foreign key ' . $foreignKey . '(' . $entity->{$foreignKey} . ' - id' . $entity->getId() . ') not found!');
            }

            $entity->setRelationEntity($foreignKey, $foreignEntities[$entity->{$foreignKey}]);
        }
    }
}
