<?php


namespace App\Core\Game\Money\Entity;


use App\Core\Game\Money\Models\IConomyModel;

class IConomy extends GameMoney
{
    public const MODEL = IConomyModel::class;

    /**
     * @inheritDoc
     */
    public function plugin(): string
    {
        return 'iconomy';
    }

    /**
     * @inheritDoc
     */
    public function getUserColumn(): string
    {
        return 'name';
    }

    /**
     * @inheritDoc
     */
    public function getCoinsColumn(): string
    {
        return 'balance';
    }
}
