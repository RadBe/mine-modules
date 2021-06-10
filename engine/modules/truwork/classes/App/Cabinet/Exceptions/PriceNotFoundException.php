<?php


namespace App\Cabinet\Exceptions;


use App\Core\Exceptions\Exception;

class PriceNotFoundException extends Exception
{
    /**
     * PriceNotFoundException constructor.
     *
     * @param string $group
     * @param int $period
     */
    public function __construct(string $group, int $period)
    {
        parent::__construct(sprintf('Цена периода %s группы %s не найдена!', $period, $group));
    }
}
