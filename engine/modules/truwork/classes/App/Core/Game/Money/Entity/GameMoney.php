<?php


namespace App\Core\Game\Money\Entity;


use App\Core\Entity\DatabaseEntity;
use App\Core\Game\Money\Models\GameMoneyModel;

abstract class GameMoney extends DatabaseEntity
{
    public const MODEL = GameMoneyModel::class;

    /**
     * @return string
     */
    abstract public function plugin(): string;

    /**
     * @return string
     */
    abstract public function getUserColumn(): string;

    /**
     * @return string
     */
    abstract public function getCoinsColumn(): string;

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->attributes[$this->getUserColumn()];
    }

    /**
     * @return int|float
     */
    public function getCoins()
    {
        return $this->attributes[$this->getCoinsColumn()];
    }

    /**
     * @param int|float $coins
     */
    public function setCoins($coins): void
    {
        $this->attributes[$this->getCoinsColumn()] = $coins;
    }

    /**
     * @param $coins
     */
    public function depositCoins($coins): void
    {
        $this->setCoins($this->getCoins() + $coins);
    }

    /**
     * @param $coins
     */
    public function withdrawCoins($coins): void
    {
        $this->setCoins($this->getCoins() - $coins);
    }
}
