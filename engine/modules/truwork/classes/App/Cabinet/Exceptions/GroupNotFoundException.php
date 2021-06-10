<?php


namespace App\Cabinet\Exceptions;


use App\Core\Exceptions\Exception;

class GroupNotFoundException extends Exception
{
    /**
     * GroupNotFoundException constructor.
     *
     * @param string $group
     */
    public function __construct(string $group)
    {
        parent::__construct(sprintf('Группа %s не найдена!', $group));
    }
}
