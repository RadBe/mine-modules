<?php


namespace App\TopVotes\Models;


use App\Core\Exceptions\Exception;
use App\Core\Models\UserModel as BaseModel;
use App\TopVotes\Entity\User;

class UserModel extends BaseModel
{
    /**
     * @param User $user
     * @throws Exception
     */
    public function addVotesCount(User $user): void
    {
        if (!$this->db->update(
            $this->createQuery()
                ->customData('`' . User::getVotesColumn() . '` = `' . User::getVotesColumn() . '` + 1')
                ->where('user_id = ?', $user->entity()->getId())
                ->limit(1)
        )) {
            throw new Exception('Не удалось добавить голоса пользователю.');
        }
    }

    /**
     * @param User $user
     */
    public function updateBonusesBalance(User $user): void
    {
        $this->db->update(
            $this->createQuery()
                ->data(User::getBonusesColumn(), $user->getBonuses())
                ->where('user_id = ?', $user->entity()->getId())
                ->limit(1)
        );
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getTopVotes(int $limit): array
    {
        return $this->db->findAll(
            $this->createQuery()
                ->select('t.' . \App\Core\Entity\User::ID_COLUMN, 't.name', 't.`' . User::getVotesColumn() . '` as month_votes', 'v.top', 'v.date_time')
                ->join('LEFT JOIN ' . TW_PREFIX . '_vote_logs v ON t.user_id = v.user_id')
                ->groupBy('t.name')
                ->orderBy('month_votes DESC, v.id ASC', '')
                ->limit($limit)
        );
    }

    /**
     * @return User[]
     */
    public function getMonthWinners(): array
    {
        $rewardsCount = count($this->app->getModule('top-votes')->getConfig()->getMonthRewards());
        if ($rewardsCount < 1) {
            return [];
        }

        $users = $this->createEntities(
            $this->createQuery()
                ->select('t.*')
                ->join('LEFT JOIN ' . TW_PREFIX . '_vote_logs v ON t.user_id = v.user_id')
                ->orderBy('t.' . User::getVotesColumn() . ' DESC, v.id ASC')
                ->limit($rewardsCount)
        );

        return array_map(function (\App\Core\Entity\User $user) {
            return new User($user);
        }, $users);
    }

    /**
     * @param User $entity
     * @return bool
     */
    public function updateEntity(User $entity): bool
    {
        return $this->db->update(
            $this->createQuery()
                ->data(User::getBonusesColumn(), $entity->getBonuses())
                ->data(User::getVotesColumn(), $entity->getVotes())
                ->where('user_id = ?', $entity->entity()->getId())
                ->limit(1)
        );

    }
}
