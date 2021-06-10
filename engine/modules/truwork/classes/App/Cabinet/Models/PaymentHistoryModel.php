<?php


namespace App\Cabinet\Models;


use App\Cabinet\Entity\PaymentHistory;
use App\Core\Database\QueryBuilder;
use App\Core\Models\EntityModel;
use App\Core\Support\Time;

class PaymentHistoryModel extends EntityModel
{
    /**
     * @inheritdoc
     */
    protected $table = 'payment_history';

    /**
     * @inheritdoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @inheritdoc
     */
    public function getEntityClass()
    {
        return PaymentHistory::class;
    }

    /**
     * @inheritDoc
     */
    public function createQuery(string $alias = 't'): QueryBuilder
    {
        return parent::createQuery($alias)
            ->select('t.*', 'u.name')
            ->join('INNER JOIN ' . USERPREFIX . '_users u ON t.user_id = u.user_id');
    }

    /**
     * @param int $perPage
     * @param string|null $name
     * @return \App\Core\Pagination\PaginatedResult|array|object[]
     */
    public function search(int $perPage = 10, ?string $name = null)
    {
        $query = $this->createQuery()->orderBy('id', 'DESC');
        $where = ['sql' => 'completed_at IS NOT NULL', 'data' => []];
        if (!empty($name)) {
            $where['sql'] .= ' AND u.name = ?';
            $where['data'][] = $name;
        }
        $query->where($where['sql'], ...$where['data']);

        return $this->paginated($query, $perPage);
    }

    /**
     * @param PaymentHistory $payment
     * @return bool
     */
    public function complete(PaymentHistory $payment): bool
    {
        $payment->completed_at = Time::now();
        return $this->db->update(
            parent::createQuery()
                ->data('completed_at', $payment->completed_at)
                ->where('id = ?', $payment->id)
                ->limit(1)
        );
    }
}
