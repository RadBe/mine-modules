<?php


namespace App\Cabinet\Entity;


use App\Core\Entity\DatabaseEntity;
use App\Core\Entity\User;
use App\Core\Support\Time;

/**
 * @property int $id
 * @property int $user_id
 * @property User $_user_id
 * @property int $amount
 * @property string $via
 * @property \DateTime|null $completed_at
 */
class PaymentHistory extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'amount', 'via'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'amount' => 'int',
        'completed_at' => 'date'
    ];

    /**
     * @param User $user
     * @param string $payer
     * @param int $amount
     * @param bool $completed
     * @return DatabaseEntity
     */
    public static function createEntity(User $user, string $payer, int $amount, bool $completed = true): DatabaseEntity
    {
        $entity = parent::create([
            'user_id' => $user->getId(),
            'amount' => $amount,
            'via' => $payer
        ]);

        $entity->completed_at = $completed ? Time::now() : null;

        return $entity;
    }
}
