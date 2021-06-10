<?php


namespace App\Core\Events;


use App\Core\Application;
use App\Core\Entity\Log;
use App\Core\Models\LogModel;

class LogListener implements Listener
{
    /**
     * @param LogEvent $event
     * @return bool|void
     */
    public function handle($event)
    {
        Application::getInstance()->make(LogModel::class)
            ->insert(Log::createEntity(
                $event->user,
                $event->server,
                $event->getContent(),
                $event->cost
            ));
    }
}
