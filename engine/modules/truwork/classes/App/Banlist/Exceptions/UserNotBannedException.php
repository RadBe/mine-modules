<?php


namespace App\Banlist\Exceptions;


use App\Core\Exceptions\Exception;

class UserNotBannedException extends Exception
{
    /**
     * UserNotBannedException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct("Игрок $name не забанен!");
    }
}
