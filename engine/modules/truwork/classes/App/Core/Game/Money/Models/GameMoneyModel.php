<?php


namespace App\Core\Game\Money\Models;


use App\Core\Entity\User;
use App\Core\Game\Money\Entity\GameMoney;
use App\Core\Models\EntityModel;

abstract class GameMoneyModel extends EntityModel
{
    /**
     * @param User $user
     * @return GameMoney|null
     */
    public function findByUser(User $user): ?GameMoney
    {
        return $this->findBy($this->getEntityClass()->getUserColumn(), $user->name);
    }
}
