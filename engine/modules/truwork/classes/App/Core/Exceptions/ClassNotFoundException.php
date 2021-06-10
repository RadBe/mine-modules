<?php


namespace App\Core\Exceptions;


class ClassNotFoundException extends Exception
{
    /**
     * ClassNotFoundException constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        parent::__construct("Class '$class' not found!");
    }
}
