<?php


namespace App\Cabinet\Exceptions;


use App\Core\Exceptions\Exception;

class PayerNotFoundException extends Exception
{
    /**
     * PayerNotFoundException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct("Метод оплаты '$name' не найден!");
    }
}
