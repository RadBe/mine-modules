<?php


namespace App\Core\Events;


interface Listener
{
    /**
     * @param $event
     * @return bool|void
     */
    public function handle($event);
}
