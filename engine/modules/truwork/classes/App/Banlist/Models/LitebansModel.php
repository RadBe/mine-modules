<?php


namespace App\Banlist\Models;


use App\Banlist\Entity\Ban;
use App\Banlist\Entity\Litebans;
use App\Core\Entity\User;

class LitebansModel extends BansModel
{
    /**
     * @inheritDoc
     */
    public function getAll(bool $paginated = true, int $perPage = 10, string $user = '')
    {
        $where = ['t.active = ? AND (t.until = -1 OR t.until > ?)', 1, $this->getEntityClass()->getNowFormat()];

        if (!empty($user)) {
            $where[0] .= ' AND h.name LIKE ?';
            $where[] = "$user%";
        }

        return $this->paginated(
            $this->createQuery()
                ->select('h.name', 't.reason', 't.banned_by_name', 't.time', 't.until')
                ->join('RIGHT JOIN litebans_history h ON t.uuid = h.uuid')
                ->where(...$where)
                ->orderBy('t.id', 'DESC'),
            $perPage);
    }

    /**
     * @inheritDoc
     */
    public function findByUser(User $user): ?Ban
    {
        $query = $this->createQuery()
            ->where('uuid = ? AND active = ? AND (until = -1 OR until > ?)', $user->getUUID(), 1, $this->getEntityClass()->getNowFormat());

        return is_null($data = $this->db->findOne($query)) ? null : $this->createEntity($data);
    }

    /**
     * @param Litebans $ban
     * @return bool
     */
    public function unban(Ban $ban): bool
    {
        return $this->db->update(
            $this->createQuery()
                ->data('active', 0)
                ->where('id = ?', $ban->getId())
                ->limit(1)
        );
    }
}
