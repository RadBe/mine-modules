<?php


namespace App\Core\Exceptions;


class ConnectionNotFoundException extends Exception
{
    /**
     * DatabaseConnectionException constructor.
     *
     * @param string $connection
     */
    public function __construct(string $connection)
    {
        parent::__construct("Соединение '$connection' не найдено!");
    }
}
