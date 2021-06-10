<?php


namespace App\Core\Database;


interface Database
{
    /**
     * @param QueryBuilder $query
     * @return array|null
     */
    public function findAll(QueryBuilder $query): ?array;

    /**
     * @param QueryBuilder $query
     * @return array|null
     */
    public function findOne(QueryBuilder $query): ?array;

    /**
     * @param QueryBuilder $query
     * @return int|string|bool
     */
    public function insert(QueryBuilder $query);

    /**
     * @param QueryBuilder $query
     * @return bool
     */
    public function update(QueryBuilder $query): bool;

    /**
     * @param QueryBuilder $query
     * @return bool
     */
    public function delete(QueryBuilder $query): bool;

    /**
     * @param string $sql
     * @param mixed ...$params
     * @return bool
     */
    public function execute(string $sql, ...$params): bool;
}
