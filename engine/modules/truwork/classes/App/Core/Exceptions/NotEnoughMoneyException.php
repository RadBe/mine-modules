<?php


namespace App\Core\Exceptions;


class NotEnoughMoneyException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(int $need)
    {
        parent::__construct(sprintf('Недостаточно средств на балансе! Необходимо: %d', $need));
    }
}
