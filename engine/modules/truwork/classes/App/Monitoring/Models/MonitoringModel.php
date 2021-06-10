<?php


namespace App\Monitoring\Models;


use App\Core\Entity\Server;
use App\Core\Models\EntityModel;
use App\Core\Support\Time;
use App\Monitoring\Entity\Monitoring;

class MonitoringModel extends EntityModel
{
    public const DAY = 600;

    public const WEEK = 7200;

    public const MONTH = 86400;

    public const MAX_DAY_TYPE = 1;

    public const MAX_MONTH_TYPE = 2;

    public const MAX_ALL_TYPE = 0;

    /**
     * @inheritDoc
     */
    protected $table = 'monitoring';

    /**
     * @inheritDoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return Monitoring::class;
    }

    /**
     * @param Server|null $server
     * @param int $group
     * @return array
     */
    public function getChart(?Server $server, int $group): array
    {
        switch ($group)
        {
            case static::WEEK: $modify = '-7 day'; break;
            case static::MONTH: $modify = '-1 month'; break;
            default: $modify = '-1 day';
        }

        $today = clone Time::now();
        $today->modify($modify);
        $query = $this->createQuery()
            ->select("FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(created_at)/$group)*$group) d, MAX(online) online")
            ->groupBy('d')
            ->orderBy('created_at');
        if (is_null($server)) {
            $query->where('server_id IS NULL AND created_at > ?', $today->format('Y-m-d H:i:s'));
        } else {
            $query->where('server_id = ? AND created_at > ?', $server->id, $today->format('Y-m-d H:i:s'));
        }

        return $this->db->findAll($query);
    }

    /**
     * @param Server|null $server
     * @param int $type
     * @return array
     */
    public function getMaxOnline(?Server $server, int $type): array
    {
        $now = clone Time::now();
        switch ($type)
        {
            case static::MAX_DAY_TYPE:
                $now->modify('-1 day');
                $date = $now->format('Y-m-d H:i');
                break;
            case static::MAX_MONTH_TYPE: $date = $now->format('Y-m') . '-01'; break;
            default: $date = null;
        }

        $query = $this->createQuery()
            ->select('MAX(online) online, created_at');
        if (is_null($server)) {
            if (is_null($date)) {
                $query->where('server_id IS NULL');
            } else {
                $query->where('server_id IS NULL AND created_at >= ?', $date);
            }
        } else {
            if (is_null($date)) {
                $query->where('server_id = ?', $server->id);
            } else {
                $query->where('server_id = ? AND created_at > ?', $server->id, $date);
            }
        }

        $result = $this->db->findOne($query);

        return [
            'online' => $result['online'] ?? 0,
            'created_at' => new \DateTimeImmutable($result['created_at'] ?? '1970-01-01 00:00:00')
        ];
    }

    /**
     * @param array $servers
     */
    public function deleteOld(array $servers): void
    {
        $now = clone Time::now();
        array_push($servers, null);
        $now->modify('-8 day');
        $to = $now->format('Y-m-d');
        $now->modify('-9 day');
        $from = $now->format('Y-m-d');
        foreach ($servers as $server)
        {
            $query = $this->createQuery()->select('MAX(online) as online');
            if (is_null($server)) {
                $query->where('created_at > ? AND created_at < ? AND server_id IS NULL', $from, $to);
            } else {
                $query->where('created_at > ? AND created_at < ? AND server_id = ?', $from, $to, $server->id);
            }
            $max = $this->db->findOne($query)['online'] ?? 0;
            $this->db->execute('DELETE FROM ' . $this->getTable() . ' WHERE created_at > ? AND created_at < ? AND online < ?', $from, $to, $max);
            echo "DELETE FROM `{$this->getTable()}` WHERE created_at > '$from' AND created_at < '$to' AND online < $max", PHP_EOL;
        }
    }
}
