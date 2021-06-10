<?php


namespace App\Core\Game\Money\Models;


use App\Core\Game\Money\Entity\IConomy;

class IConomyModel extends GameMoneyModel
{
    /**
     * @inheritDoc
     */
    public $table = 'money';

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return new IConomy();
    }
}
