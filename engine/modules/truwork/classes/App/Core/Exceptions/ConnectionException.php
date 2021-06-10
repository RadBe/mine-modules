<?php


namespace App\Core\Exceptions;


class ConnectionException extends Exception
{
    /**
     * ConnectionException constructor.
     *
     * @param string $error
     */
    public function __construct(string $error)
    {
        parent::__construct('Не удалось подключиться к бд! ' . $error);
    }
}
