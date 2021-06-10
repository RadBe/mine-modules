<?php


namespace App\Core\Exceptions;


class CsrfException extends Exception
{
    /**
     * CsrfException constructor.
     */
    public function __construct()
    {
        parent::__construct('Неправильный csrf код.');
    }
}
