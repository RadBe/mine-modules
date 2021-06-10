<?php


namespace App\Core\Exceptions;


class ServerNotFoundException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Сервер %d не найден!', $id));
    }
}
