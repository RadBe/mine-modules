<?php


namespace App\TopVotes\Exceptions;


use App\Core\Exceptions\Exception;

class InvalidRequestDataException extends Exception
{
    /**
     * InvalidRequestDataException constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        parent::__construct("Отсутствует или невалидный параметр запроса '$key'");
    }
}
