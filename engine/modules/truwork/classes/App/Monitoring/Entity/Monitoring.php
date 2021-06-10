<?php


namespace App\Monitoring\Entity;


use App\Core\Entity\DatabaseEntity;
use App\Core\Entity\Server;
use App\Core\Support\Time;

/**
 * @property int $id
 * @property int|null $server_id
 * @property \App\Core\Entity\Server|null $_server_id
 * @property int $online
 * @property \DateTime $created_at
 */
class Monitoring extends DatabaseEntity
{
    /**
     * @inheritDoc
     */
    protected $fillable = [
        'server_id', 'online'
    ];

    /**
     * @inheritDoc
     */
    protected $casts = [
        'created_at' => 'date'
    ];

    /**
     * @param Server|null $server
     * @param int $online
     * @return DatabaseEntity
     */
    public static function createEntity(?Server $server, int $online): DatabaseEntity
    {
        $entity = parent::create([
            'server_id' => $server->id,
            'online' => $online
        ]);
        $entity->created_at = Time::now();

        return $entity;
    }
}
