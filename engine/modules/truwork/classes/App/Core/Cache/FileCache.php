<?php


namespace App\Core\Cache;


use App\Core\Support\Time;
use Closure;
use Exception;

class FileCache implements CacheSystem
{
    public const DIR = ENGINE_DIR . '/cache/truwork';

    /**
     * @inheritDoc
     */
    public function put(string $key, $value, int $seconds = 0): void
    {
        $this->ensureCacheDirectoryExists($path = $this->path($key));

        file_put_contents($path, $this->expiration($seconds) . serialize($value));
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        return $this->getPayLoad($key)['data'] ?: null;
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
        if (is_file($path = $this->path($key))) {
            unlink($path);
        }
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        if (is_dir(static::DIR)) {
            $this->removeCacheDir(static::DIR);
        }
    }

    /**
     * @param string $path
     */
    private function ensureCacheDirectoryExists(string $path): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function path(string $key): string
    {
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);

        return static::DIR . '/' . implode('/', $parts) . '/' . $hash;
    }

    /**
     * @param string $dir
     */
    private function removeCacheDir(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file)
        {
            $file = $dir . '/' . $file;
            if (is_dir($file)) {
                $this->removeCacheDir($file);
                @rmdir($file);
            } else {
                @unlink($file);
            }
        }
    }

    /**
     * @param int $seconds
     * @return int
     */
    protected function expiration(int $seconds): int
    {
        $time = Time::now()->getTimestamp() + $seconds;

        return $seconds === 0 || $time > 9999999999 ? 9999999999 : $time;
    }

    /**
     * @param string $key
     * @return array
     */
    protected function getPayLoad(string $key): array
    {
        $path = $this->path($key);

        try {
            $expire = substr(
                $contents = file_get_contents($path), 0, 10
            );
        } catch (Exception $e) {
            return $this->emptyPayload();
        }

        if ($now = Time::now()->getTimestamp() > $expire) {
            $this->forget($key);

            return $this->emptyPayLoad();
        }

        try {
            $data = unserialize(substr($contents, 10));
        } catch (Exception $e) {
            $this->forget($key);

            return $this->emptyPayload();
        }

        $time = $expire - $now;

        return compact('data', 'time');
    }

    /**
     * @return array
     */
    protected function emptyPayLoad(): array
    {
        return ['data' => null, 'time' => null];
    }
}
