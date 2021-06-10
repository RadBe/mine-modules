<?php


namespace App\Cabinet\Services\Payment\Payers\Unitpay;


class Payment
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var int
     */
    private $cost;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $currency = 'rub';

    /**
     * Payment constructor.
     *
     * @param string $username
     * @param int $cost
     * @param string $description
     */
    public function __construct(string $username, int $cost, string $description)
    {
        $this->username = $username;
        $this->cost = $cost;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }
}
