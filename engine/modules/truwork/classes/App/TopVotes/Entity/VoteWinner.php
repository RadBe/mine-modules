<?php


namespace App\TopVotes\Entity;


use App\Core\Entity\DatabaseEntity;

/**
 * @property int $id
 * @property int $user_id
 * @property \App\Core\Entity\User $_user_id
 * @property int $rank
 * @property int $votes
 * @property string $month
 */
class VoteWinner extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'rank', 'votes', 'month'
    ];
}
