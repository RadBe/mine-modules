<?php


namespace App\Promo\Entity;


use App\Core\Entity\DatabaseEntity;

/**
 * @property string $code
 * @property int $amount
 */
class Promo extends DatabaseEntity
{
    public const ID_COLUMN = 'code';

    public const AUTOINCREMENT = false;
}
