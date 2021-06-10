<?php


namespace App\Shop\Exceptions;


use App\Core\Exceptions\Exception;

class CategoryNotFoundException extends Exception
{
    /**
     * CategoryNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Категория #%d не найдена!', $id));
    }
}
