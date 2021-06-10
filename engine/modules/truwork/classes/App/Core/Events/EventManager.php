<?php


namespace App\Core\Events;


use App\Core\Application;

class EventManager
{
    /**
     * @var array
     */
    protected static $events = [];

    /**
     * @param object $event
     */
    protected static function callEvent(object $event): void
    {
        foreach (static::$events[get_class($event)] as $listenerClass)
        {
            if (Application::getInstance()->make($listenerClass)->handle($event) === false)
                break;
        }
    }

    /**
     * @param string $event
     * @param string $listener
     */
    public static function register(string $event, string $listener): void
    {
        if (!isset(static::$events[$event])) {
            static::$events[$event] = [];
        }

        static::$events[$event][] = $listener;
    }

    /**
     * @param string $event
     */
    public static function registerLog(string $event): void
    {
        static::register($event, LogListener::class);
    }

    /**
     * @param object $event
     */
    public static function dispatch(object $event): void
    {
        if (!isset(static::$events[get_class($event)])) return;

        static::callEvent($event);
    }

    /**
     * EventManager constructor.
     */
    private function __construct(){}
}
