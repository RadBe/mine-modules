<?php


namespace App\Cabinet\Services\Payment\Payers\Freekassa;


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
     * Payment constructor.
     *
     * @param string $username
     * @param int $cost
     */
    public function __construct(string $username, int $cost)
    {
        $this->username = $username;
        $this->cost = $cost;
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
}
