<?php


namespace App\Core\Cache;


use App\Core\Application;

/**
 * @method static void put(string $key, $value, int $seconds = 0)
 * @method static mixed get(string $key)
 * @method static mixed remember(string $key, \Closure $value, int $seconds = 0)
 * @method static void forget(string $key)
 * @method static void flush()
 */
class Cache
{
    /**
     * @var array
     */
    protected static $drivers = [];

    /**
     * Cache constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param string|null $class
     * @return CacheSystem
     */
    public static function driver(?string $class = null): CacheSystem
    {
        if (is_null($class)) {
            $class = CacheSystem::class;
        }

        return is_null(static::$drivers[$class])
            ? static::$drivers[$class] = Application::getInstance()->make($class)
            : static::$drivers[$class];
    }

    /**
     * @return FileCache
     */
    public static function file(): FileCache
    {
        return static::driver(FileCache::class);
    }

    /**
     * @return SkinCache
     */
    public static function skin(): SkinCache
    {
        return static::driver(SkinCache::class);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return static::driver()->$method(...$args);
    }
}
