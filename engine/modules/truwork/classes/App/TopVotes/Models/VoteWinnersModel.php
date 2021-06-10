<?php


namespace App\TopVotes\Models;


use App\Core\Models\EntityModel;
use App\Core\Support\AttachRelationEntity;
use App\Core\Support\Time;
use App\TopVotes\Entity\VoteWinner;

class VoteWinnersModel extends EntityModel
{
    /**
     * @inheritDoc
     */
    protected $table = 'vote_winners';

    /**
     * @inheritDoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return VoteWinner::class;
    }

    /**
     * @return array
     * @throws \App\Core\Exceptions\Exception
     */
    public function getPrevMonthWinners(): array
    {
        $now = clone Time::now();
        $now->modify('-1 month');

        $query = $this->createQuery()
            ->where('t.`month` = ?', $now->format('Y-m-') . '01')
            ->orderBy('`rank`', 'ASC');

        $result = is_null($rows = $this->db->findAll($query))
            ? []
            : $this->createEntities($rows);
        AttachRelationEntity::make($result, $this->app->make(\App\Core\Models\UserModel::class), 'user_id');

        return $result;
    }
}
