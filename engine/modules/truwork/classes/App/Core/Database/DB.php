<?php


namespace App\Core\Database;


use App\Core\Application;
use App\Core\Exceptions\ConnectionException;
use App\Core\Exceptions\ConnectionNotFoundException;

class DB
{
    /**
     * @var bool
     */
    public static $displayErrors = true;

    /**
     * @var bool
     */
    public static $debug = false;

    /**
     * @var DBConfig
     */
    private static $config;

    /**
     * @var Database[]
     */
    private static $connections = [];

    /**
     * DB constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return void
     */
    public static function init(): void
    {
        static::$config = new DBConfig();
    }

    /**
     * @param string|null $name
     * @return Database
     * @throws ConnectionException
     * @throws ConnectionNotFoundException
     */
    public static function getConnection(?string $name = null): Database
    {
        if (is_null($name)) {
            return Application::getInstance()->getBaseDBConnection();
        }

        if (isset(static::$connections[$name])) {
            return static::$connections[$name];
        }

        $data = static::$config->getConnectionData($name);

        $db = new \db();
        if (!$db->connect(
            $data['user'],
            $data['password'],
            $data['dbname'],
            $data['host'],
            0
        )) {
            throw new ConnectionException($db->query_errors_list[0]['error']);
        }
        $driverClass = '\App\Core\Database\\' . $data['driver'];
        static::$connections[$name] = new $driverClass($db);

        return static::$connections[$name];
    }

    /**
     * @param int $serverId
     * @return Database
     * @throws ConnectionException
     * @throws ConnectionNotFoundException
     */
    public static function getServerConnection(int $serverId): Database
    {
        return static::getConnection('server_' . $serverId);
    }

    /**
     * @return DBConfig
     */
    public static function getConfig(): DBConfig
    {
        return static::$config;
    }
}
