<?php


namespace App\Core\VK;


use App\Core\Application;
use App\Core\Exceptions\ClassNotFoundException;
use App\Core\Support\Str;

class EventManager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * EventManager constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $name
     * @return string
     * @throws ClassNotFoundException
     */
    private function getEventClass(string $name): string
    {
        $class = '\App\Core\VK\Events\\' . Str::studly($name);
        if (class_exists($class)) {
            return $class;
        }

        throw new ClassNotFoundException($class);
    }

    /**
     * @param string $type
     * @param array $data
     * @param int $groupId
     * @throws ClassNotFoundException
     */
    public function callEvent(string $type, array $data, int $groupId): void
    {
        $eventClass = $this->getEventClass($type);
        $event = $this->app->make($eventClass, $data, $groupId);
        dispatch($event);
    }
}
