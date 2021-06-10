<?php


namespace App\Core\Entity;


use App\Core\Support\Time;

/**
 * @property int $id
 * @property int $user_id
 * @property User $_user_id
 * @property int|null $server_id
 * @property Server|null $_server_id
 * @property string $content
 * @property int $cost
 * @property \DateTime $created_at
 */
class Log extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $casts = [
        'created_at' => 'date'
    ];

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'server_id', 'content', 'cost', 'created_at'
    ];

    /**
     * @param User $user
     * @param Server|null $server
     * @param string $content
     * @param int $cost
     * @return DatabaseEntity
     */
    public static function createEntity(User $user, ?Server $server, string $content, int $cost): DatabaseEntity
    {
        return parent::create([
            'user_id' => $user->getId(),
            'server_id' => is_null($server) ? null : $server->getId(),
            'content' => $content,
            'cost' => $cost,
            'created_at' => Time::now()
        ]);
    }
}
