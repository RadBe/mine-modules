<?php


namespace App\Cabinet\Exceptions;


use App\Core\Exceptions\Exception;

class PeriodExtendException extends Exception
{
    /**
     * PeriodExtendException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Группу %s больше некуда продлевать!', $name));
    }
}
