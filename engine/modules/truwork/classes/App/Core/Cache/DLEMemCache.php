<?php


namespace App\Core\Cache;


use App\Core\Support\Time;
use Closure;

class DLEMemCache implements CacheSystem
{
    /**
     * @var \Memcached|\Memcache
     */
    protected $memcache;

    /**
     * @var bool
     */
    protected $isMemCached;

    /**
     * DLEMemCache constructor.
     *
     * @param \Memcached|\Memcache $memcache
     */
    public function __construct($memcache)
    {
        $this->memcache = $memcache;
        $this->isMemCached = $this->memcache instanceof \Memcached;
    }

    /**
     * @inheritDoc
     */
    public function put(string $key, $value, int $seconds = 0): void
    {
        if ($this->isMemCached) {
            $this->memcache->set($key, $value, $this->getTimeout($seconds));
        } else {
            $this->memcache->set($key, $value, null, $this->getTimeout($seconds));
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        $value = $this->memcache->get($key);
        if ($this->isMemCached) {
            if ($this->memcache->getResultCode() != \Memcached::RES_NOTFOUND) {
                return $value;
            }
        } else {
            if ($value !== false) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function remember(string $key, Closure $value, int $seconds = 0)
    {
        $cache = $this->get($key);
        if (!is_null($cache)) {
            return $cache;
        }

        $this->put($key, $cache = $value(), $seconds);

        return $cache;
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): void
    {
        $this->memcache->delete($key);
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        $this->memcache->flush();
    }

    /**
     * @param int $seconds
     * @return int
     */
    protected function getTimeout(int $seconds): int
    {
        if ($seconds < 1) return 0;
        elseif ($seconds >= 2592000) return Time::now()->getTimestamp() + $seconds; //если больше 30 дней
        else return $seconds;
    }
}
