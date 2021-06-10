<?php


namespace App\Promo\Models;


use App\Promo\Entity\Promo;
use App\Core\Models\EntityModel;

class PromoModel extends EntityModel
{
    /**
     * @inheritDoc
     */
    protected $table = 'promos';

    /**
     * @inheritDoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return Promo::class;
    }
}
