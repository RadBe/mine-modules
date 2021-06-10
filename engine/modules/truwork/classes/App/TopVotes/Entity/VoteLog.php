<?php


namespace App\TopVotes\Entity;


use App\Core\Entity\DatabaseEntity;
use App\Core\Entity\User;
use App\Core\Support\Time;

/**
 * @property int $id
 * @property int $user_id
 * @property User $_user_id
 * @property string $top
 * @property string $date_day
 * @property \DateTime $date_time
 */
class VoteLog extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'top'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'date_time' => 'date'
    ];

    /**
     * @param User $user
     * @param string $top
     * @return VoteLog
     */
    public static function create(User $user, string $top): DatabaseEntity
    {
        $log = parent::create([
            'user_id' => $user->getId(),
            'top' => $top
        ]);

        $log->setRelationEntity('user_id', $user);
        $log->date_day = Time::now()->format('Y-m-d');
        $log->date_time = Time::now();

        return $log;
    }
}
