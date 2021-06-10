<?php


namespace App\Core\Database;


use App\Core\Exceptions\DatabaseException;

class MySQLi implements Database
{
    public static $DEBUG = false;

    /**
     * @var \db
     */
    private $connection;

    /**
     * DB constructor.
     *
     * @param \db $connection
     */
    public function __construct(\db $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * @param string $sql
     * @throws DatabaseException
     */
    private function displayError(string $sql): void
    {
        if (!DB::$displayErrors) {
            throw new DatabaseException(
                mysqli_errno($this->connection->db_id),
                mysqli_error($this->connection->db_id),
                $sql
            );
        }

        $this->connection->display_error(
            mysqli_error($this->connection->db_id),
            mysqli_errno($this->connection->db_id),
            $sql
        );
    }

    /**
     * @param array $values
     * @return string
     */
    private function getParamTypes(array $values): string
    {
        $result = '';
        foreach ($values as $value)
        {
            $result .= $this->getTypeByValue($value);
        }

        return $result;
    }

    /**
     * @param $value
     * @return string
     */
    private function getTypeByValue($value): string
    {
        switch (gettype($value))
        {
            case 'double': return 'd';
            case 'integer': return 'i';
            default: return 's';
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return false|\mysqli_stmt
     */
    private function prepare(string $sql, array $params = [])
    {
        if (DB::$debug) {
            print '<p style="color: red">' . $sql . '</p>';
            var_dump($params);
        }

        $stmt = mysqli_prepare($this->connection->db_id, $sql);
        if (!$stmt) {
            $this->displayError($sql);
        }

        if (!empty($params) && !$stmt->bind_param($this->getParamTypes($params), ...$params)) {
            $this->displayError($sql);
        }

        return $stmt;
    }
    
    /**
     * @param QueryBuilder $query
     * @return string
     */
    private function setFields(QueryBuilder $query): string
    {
        $sql = '';

        foreach (array_keys($query->getData()) as $key) {
            $sql .= '`' . $key . '` = ? , ';
        }

        foreach ($query->getCustomData() as $key) {
            $sql .= '' . $key . ' , ';
        }

        $sql = rtrim($sql, ', ');

        return $sql;
    }

    /**
     * @param QueryBuilder $query
     * @param string $sql
     * @return string
     */
    private function addJoins(QueryBuilder $query, string &$sql): string
    {
        if ($query->getJoins()) {
            $sql .= implode(' ' , $query->getJoins());
        }

        return $sql;
    }

    /**
     * @param QueryBuilder $query
     * @param string $sql
     * @return string
     */
    private function addWheres(QueryBuilder $query, string &$sql): string
    {
        if($query->getWhere()) {
            $sql .= ' WHERE ' . $query->getWhere();
        }

        return $sql;
    }

    /**
     * @param QueryBuilder $query
     * @param string $sql
     * @return string
     */
    private function addOrders(QueryBuilder $query, string &$sql): string
    {
        if ($query->getOrderBy()) {
            $sql .= ' ORDER BY ' . implode(' ' , $query->getOrderBy());
        }

        return $sql;
    }

    /**
     * @param QueryBuilder $query
     * @param string $sql
     * @return string
     */
    private function addLimit(QueryBuilder $query, string &$sql): string
    {
        if($query->getLimit() > 0) {
            $sql .= ' LIMIT ' . $query->getLimit();
        }

        return $sql;
    }

    /**
     * @param QueryBuilder $query
     * @param string $sql
     * @return string
     */
    private function addOffset(QueryBuilder $query, string &$sql): string
    {
        if ($query->getOffset() > 0) {
            $sql .= ' OFFSET ' . $query->getOffset();
        }

        return $sql;
    }

    /**
     * @param QueryBuilder $query
     * @param string $sql
     * @return string
     */
    private function addGroupBy(QueryBuilder $query, string &$sql): string
    {
        if ($query->getGroupBy()) {
            $sql .= ' GROUP BY ' . implode(' ' , $query->getGroupBy());
        }

        return $sql;
    }

    /**
     * @param QueryBuilder $query
     * @return string
     */
    private function fetchStatement(QueryBuilder $query): string
    {
        $sql = 'SELECT ';

        if ($query->getSelects()) {
            $sql .= implode(',' , $query->getSelects());
        } else {
            $sql .= '*';
        }

        $sql .= ' FROM ' . $query->getTable() . " {$query->getTableAlias()} ";

        $this->addJoins($query, $sql);

        $this->addWheres($query, $sql);

        $this->addGroupBy($query, $sql);

        $this->addOrders($query, $sql);

        $this->addLimit($query, $sql);

        $this->addOffset($query, $sql);

        /*print "<p style='color: red'>$sql</p>";
        print '<p style="color: blue">' . var_export($query->getBindings(), true) . '</p>';*/

        return $sql;
    }

    /**
     * @inheritDoc
     */
    public function findOne(QueryBuilder $query): ?array
    {
        $query->limit(1);

        $sql = $this->fetchStatement($query);
        
        $stmt = $this->prepare($sql, $query->getBindings());
        if ($stmt->execute() && ($result = $stmt->get_result())) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function findAll(QueryBuilder $query): ?array
    {
        $sql = $this->fetchStatement($query);

        $stmt = $this->prepare($sql, $query->getBindings());
        if ($stmt->execute() && ($result = $stmt->get_result())) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function insert(QueryBuilder $query)
    {
        $sql = 'INSERT INTO ' . $query->getTable() . ' SET ' . $this->setFields($query);

        $stmt = $this->prepare($sql, $query->getBindings());
        if (!$stmt->execute()) {
            $this->displayError($sql);

            return false;
        }

        return mysqli_insert_id($this->connection->db_id);
    }

    /**
     * @inheritDoc
     */
    public function update(QueryBuilder $query): bool
    {
        $sql = 'UPDATE ' . $query->getTable() . ' SET ' . $this->setFields($query);

        $this->addWheres($query, $sql);

        $this->addLimit($query, $sql);

        $stmt = $this->prepare($sql, $query->getBindings());
        if (!$stmt->execute()) {
            $this->displayError($sql);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(QueryBuilder $query): bool
    {
        $sql = 'DELETE FROM ' . $query->getTable();

        $this->addWheres($query, $sql);

        $this->addLimit($query, $sql);

        $stmt = $this->prepare($sql, $query->getBindings());
        if (!$stmt->execute()) {
            $this->displayError($sql);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $sql, ...$params): bool
    {
        if (!$this->prepare($sql, $params)->execute()) {
            $this->displayError($sql);
        }

        return true;
    }
}
