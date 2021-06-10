<?php


namespace App\Core\Exceptions;


class InvalidPasswordException extends Exception
{
    /**
     * InvalidPasswordException constructor.
     */
    public function __construct()
    {
        parent::__construct('Неправильный пароль!');
    }
}
