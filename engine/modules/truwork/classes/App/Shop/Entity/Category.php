<?php


namespace App\Shop\Entity;


use App\Core\Entity\DatabaseEntity;
use App\Core\Entity\Server;

/**
 * @property int $id
 * @property Server|null $_server_id
 * @property int|null $server_id
 * @property string $name
 * @property bool $enabled
 */
class Category extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'server_id', 'name', 'enabled'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'enabled' => 'bool'
    ];
}
