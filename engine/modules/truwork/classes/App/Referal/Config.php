<?php


namespace App\Referal;


use App\Core\Config\Config as BaseConfig;

class Config extends BaseConfig
{
    /**
     * @return int 0-100
     */
    public function getReferalRate(): int
    {
        return $this->data['rate'] ?? 0;
    }

    /**
     * @param int $rate
     */
    public function setReferalRate(int $rate): void
    {
        $this->data['rate'] = $rate;
    }
}
