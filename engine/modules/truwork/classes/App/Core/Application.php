<?php


namespace App\Core;


use App\Core\Cache\Cache;
use App\Core\Database\Database;
use App\Core\Database\DB;
use App\Core\Entity\Module as BaseModule;
use App\Core\Entity\User;
use App\Core\Http\Middleware\Middleware;
use App\Core\Http\Request;
use App\Core\Models\LogModel;
use App\Core\Models\ModulesModel;
use App\Core\Models\UserModel;
use App\Core\User\UUID\Generator\Generator;
use App\DLEConfig;
use Closure;

class Application
{
    public const VERSION = '1.1';

    /**
     * @var Application
     */
    private static $instance;

    /**
     * @var bool
     */
    public static $ajaxMode = false;

    /**
     * @var bool
     */
    public static $asAdmin = false;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var BaseModule[]
     */
    private $modules;

    /**
     * @var array
     */
    private $findedModules = [];

    /**
     * @var Module
     */
    private $core;

    /**
     * @var array
     */
    private $middlewares = [];

    /**
     * @var array
     */
    private $bindings = [];

    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var DLEConfig
     */
    private $dleConfig;

    /**
     * Application constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param Database $baseConnection
     * @param array|null $userData
     * @return $this
     */
    public function init(Database $baseConnection, DLEConfig $dleConfig, ?array $userData): self
    {
        if (!is_null($this->core)) {
            return $this;
        }

        $this->db = $baseConnection;
        $this->dleConfig = $dleConfig;

        $this->request = $this->make(Request::class);
        DB::init();

        $this->loadModules();
        $this->core = $this->getModule('core');

        $this->singleton(LogModel::class);

        if ($this->core->isInstalled()) {
            $this->singleton(Generator::class, $this->getConfig()->getUUIDGenerator());
        }

        if (!empty($userData) && is_array($userData) && isset($userData['user_id'])) {
            $this->user = UserModel::createUserEntity($userData);
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function loadModules(): void
    {
        $this->modules = Cache::remember('truwork_modules', function () {
            return (new ModulesModel($this))
                ->getAll();
        });
    }

    /**
     * @param string $abstract
     * @param bool $singleton
     * @param Closure|object|null $concrete
     */
    public function bind(string $abstract, $concrete = null, bool $singleton = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'singleton');
    }

    /**
     * @param string $abstract
     * @param null $concrete
     */
    public function singleton(string $abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * @param string $abstract
     * @param mixed ...$parameters
     * @return mixed
     */
    public function make(string $abstract, ...$parameters)
    {
        $emptyParams = empty($parameters);
        if ($emptyParams) {
            array_unshift($parameters, $this);
        }

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            return new $abstract(...$parameters);
        }

        $binding = $this->bindings[$abstract];

        $obj = $binding['concrete'] instanceof Closure
            ? call_user_func($binding['concrete'], $this, ...$parameters)
            : (is_object($binding['concrete']) ? $binding['concrete'] : new $binding['concrete'](...$parameters));

        if ($binding['singleton'] && $emptyParams) {
            $this->instances[$abstract] = $obj;
        }

        return $obj;
    }

    /**
     * @return Application
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            return self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return Database
     */
    public function getBaseDBConnection(): Database
    {
        return $this->db;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return BaseModule[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @param string $id
     * @return BaseModule|null
     */
    public function getModule(string $id): ?BaseModule
    {
        if (isset($this->findedModules[$id])) {
            return $this->findedModules[$id];
        }

        foreach ($this->modules as $module)
        {
            if ($module->getId() == $id) {
                return $this->findedModules[$module->getId()] = $module;
            }
        }

        return null;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasModule(string $id): bool
    {
        foreach ($this->modules as $module)
        {
            if ($module->getId() == $id) {
                return $module->isInstalled() && $module->isEnabled();
            }
        }

        return false;
    }

    /**
     * @return Module
     */
    public function getCore(): Module
    {
        return $this->core;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->core->getConfig();
    }

    /**
     * @return DLEConfig
     */
    public function getDLEConfig(): DLEConfig
    {
        return $this->dleConfig;
    }

    /**
     * @param Middleware $middleware
     * @param mixed ...$params
     */
    public function addMiddleware(Middleware $middleware, ...$params): void
    {
        $this->middlewares[] = [$middleware, $params];
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
