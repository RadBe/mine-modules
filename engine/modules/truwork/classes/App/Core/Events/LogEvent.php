<?php


namespace App\Core\Events;


use App\Core\Application;
use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Services\Discord\Embed;
use App\Core\Services\Discord\Message;
use App\Core\Services\SkinManager;
use App\Core\Support\Str;
use App\Core\Support\Time;

abstract class LogEvent
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var Server|null
     */
    public $server = null;

    /**
     * @var int
     */
    public $cost = 0;

    /**
     * @return string
     */
    abstract public function getContent(): string;
}
