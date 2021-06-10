<?php


namespace App\Core\Database;


use App\Core\Exceptions\ConnectionNotFoundException;

class DBConfig
{
    protected const FILE = TW_DIR . '/configs/db.php';

    /**
     * @var array
     */
    protected $data;

    /**
     * DBConfig constructor.
     */
    public function __construct()
    {
        $this->data = require_once static::FILE;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @return array
     * @throws ConnectionNotFoundException
     */
    public function getConnectionData(string $name): array
    {
        if (!isset($this->data[$name])) {
            throw new ConnectionNotFoundException($name);
        }

        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     */
    public function addConnection(string $name, string $host, string $user, string $password, string $dbname): void
    {
        $this->data[$name] = array_merge(['driver' => 'MySQLi'], compact('host', 'user', 'password', 'dbname'));
    }

    /**
     * @param string $name
     */
    public function removeConnection(string $name): void
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    /**
     * @return void
     */
    public function save(): void
    {
        file_put_contents(static::FILE, "<?php\n\nreturn " . var_export($this->data, true) . ";\n");
    }
}
