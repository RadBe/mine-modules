<?php


namespace App\Core\Support;


use DateTime;

class Time
{
    /**
     * @var DateTime
     */
    private static $now;

    /**
     * Time constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return DateTime
     */
    public static function now(): DateTime
    {
        if (is_null(static::$now)) {
            return static::$now = new DateTime();
        }

        return static::$now;
    }
}
