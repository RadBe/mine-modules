<?php


namespace App\Shop\Entity;


use App\Core\Entity\DatabaseEntity;
use App\Core\Entity\User;
use App\Core\Support\Time;

/**
 * @property int $id
 * @property int $user_id
 * @property User $_user_id
 * @property int $product_id
 * @property Product $_product_id
 * @property int $amount
 * @property int $price
 * @property \DateTime $created_at
 */
class Warehouse extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'product_id', 'amount', 'price'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'created_at' => 'date',
        'give_at' => 'date',
    ];

    /**
     * @param User $user
     * @param Product $product
     * @param int $amount
     * @param int $price
     * @return DatabaseEntity
     */
    public static function createEntity(User $user, Product $product, int $amount, int $price): DatabaseEntity
    {
        $entity = parent::create([
            'user_id' => $user->getId(),
            'product_id' => $product->id,
            'amount' => $amount,
            'price' => $price
        ]);
        $entity->created_at = Time::now();
        $entity->setRelationEntity('user_id', $user);
        $entity->setRelationEntity('product_id', $product);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        if ($this->hasRelationEntity('product_id')) {
            $data['product'] = $this->_product_id->toArray();
        }

        return $data;
    }
}
