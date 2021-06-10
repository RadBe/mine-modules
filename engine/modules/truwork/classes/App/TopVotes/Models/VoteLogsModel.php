<?php


namespace App\TopVotes\Models;


use App\Core\Models\EntityModel;
use App\Core\Support\Time;
use App\TopVotes\Entity\User;
use App\TopVotes\Entity\VoteLog;

class VoteLogsModel extends EntityModel
{
    /**
     * @inheritdoc
     */
    protected $table = 'vote_logs';

    /**
     * @inheritdoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @inheritdoc
     */
    public function getEntityClass()
    {
        return VoteLog::class;
    }

    /**
     * @param User $user
     * @return VoteLog[]
     */
    public function getTodayVotesByUser(User $user): array
    {
        return $this->createEntities(
            $this->db->findAll(
                $this->createQuery()
                    ->where('user_id = ? AND date_day = ?', $user->entity()->getId(), Time::now()->format('Y-m-d'))
            )
        );
    }
}
