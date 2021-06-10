<?php


namespace App\Core\Game\Money\Models;


use App\Core\Game\Money\Entity\FeEconomy;

class FeEconomyModel extends GameMoneyModel
{
    /**
     * @inheritdoc
     */
    protected $table = 'money';

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return new FeEconomy();
    }
}
