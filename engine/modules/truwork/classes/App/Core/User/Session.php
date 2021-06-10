<?php


namespace App\Core\User;


class Session
{
    /**
     * Session constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param string $key
     * @param $value
     */
    public static function put(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public static function getAndForget(string $key)
    {
        $val = static::get($key);
        static::forget($key);

        return $val;
    }

    /**
     * @param string $key
     */
    public static function forget(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}
