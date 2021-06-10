<?php


namespace App\Shop\Excetptions;


use App\Core\Exceptions\Exception;

class EnchantNotFoundException extends Exception
{
    /**
     * EnchantNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct("Зачарование '$id' не найдено!");
    }
}
