<?php


namespace App\Core\Models;


use App\Core\Database\QueryBuilder;
use App\Core\Entity\Log;
use App\Core\Pagination\PaginatedResult;
use App\Core\Support\Str;

class LogModel extends EntityModel
{
    public const PER_PAGE = 15;

    /**
     * @inheritDoc
     */
    protected $table = 'logs';

    /**
     * @inheritDoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return Log::class;
    }

    /**
     * @param int|null $userId
     * @param int|null $serverId
     * @param bool|null $onlyCost
     * @return PaginatedResult
     */
    public function search(?int $userId = null, ?int $serverId = null, ?bool $onlyCost = null): PaginatedResult
    {
        $query = $this->createQuery()->orderBy('id', 'DESC');
        $where = [
            'sql' => '',
            'data' => []
        ];

        if (!is_null($userId)) {
            $where['sql'] .= 'AND user_id = ? ';
            $where['data'][] = $userId;
        }

        if (!is_null($serverId)) {
            $where['sql'] .= 'AND (server_id = ? OR server_id IS NULL) ';
            $where['data'][] = $serverId;
        }

        if (!is_null($onlyCost)) {
            $where['sql'] .= 'AND cost' . ($onlyCost ? ' > 0' : ' = 0');
        }

        if (!empty($where['sql'])) {
            $query->where(Str::substr($where['sql'], 3), ...$where['data']);
        }

        return $this->paginated($query, static::PER_PAGE);
    }
}
