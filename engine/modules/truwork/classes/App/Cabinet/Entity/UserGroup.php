<?php


namespace App\Cabinet\Entity;


use App\Core\Entity\DatabaseEntity;
use App\Core\Entity\Server;

/**
 * @property int $id
 * @property int $user_id
 * @property \App\Core\Entity\User $_user_id
 * @property string $group_name
 * @property int $server_id
 * @property Server $_server_id
 * @property int $expiry
 */
class UserGroup extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id', 'group_name', 'server_id', 'expiry'
    ];

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'expiry_date' => $this->expiry > 0 ? date('d.m.Y H:i', $this->attributes['expiry']) : 0
        ]);
    }
}
