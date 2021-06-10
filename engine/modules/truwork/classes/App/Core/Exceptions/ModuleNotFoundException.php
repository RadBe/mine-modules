<?php


namespace App\Core\Exceptions;


class ModuleNotFoundException extends Exception
{
    /**
     * ModuleNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('Модуль не найден!');
    }
}
