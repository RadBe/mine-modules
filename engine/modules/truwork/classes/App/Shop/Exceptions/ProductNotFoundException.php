<?php


namespace App\Shop\Exceptions;


use App\Core\Exceptions\Exception;

class ProductNotFoundException extends Exception
{
    /**
     * ProductNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Товар #%d не найден!', $id));
    }
}
