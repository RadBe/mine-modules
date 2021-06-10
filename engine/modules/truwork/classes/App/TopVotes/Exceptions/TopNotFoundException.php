<?php


namespace App\TopVotes\Exceptions;


use App\Core\Exceptions\Exception;

class TopNotFoundException extends Exception
{
    /**
     * TopNotFoundException constructor.
     *
     * @param string $top
     */
    public function __construct(string $top)
    {
        parent::__construct("Топ '$top' не найден!");
    }
}
