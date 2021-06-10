<?php


namespace App\Core\Cache;


use Closure;

interface CacheSystem
{
    /**
     * @param string $key
     * @param mixed $value
     * @param int $seconds
     */
    public function put(string $key, $value, int $seconds = 0): void;

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @param Closure $value
     * @param int $seconds
     * @return mixed
     */
    public function remember(string $key, Closure $value, int $seconds = 0);

    /**
     * @param string $key
     */
    public function forget(string $key): void;

    /**
     * @return void
     */
    public function flush(): void;
}
