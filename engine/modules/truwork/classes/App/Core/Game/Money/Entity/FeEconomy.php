<?php


namespace App\Core\Game\Money\Entity;


use App\Core\Game\Money\Models\FeEconomyModel;

class FeEconomy extends GameMoney
{
    public const MODEL = FeEconomyModel::class;

    public const ID_COLUMN = 'username';

    /**
     * @inheritDoc
     */
    public function plugin(): string
    {
        return 'feeconomy';
    }

    /**
     * @inheritDoc
     */
    public function getUserColumn(): string
    {
        return 'username';
    }

    /**
     * @inheritDoc
     */
    public function getCoinsColumn(): string
    {
        return 'balance';
    }
}
