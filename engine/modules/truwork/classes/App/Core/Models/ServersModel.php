<?php


namespace App\Core\Models;


use App\Core\Entity\Server;
use App\Core\Http\UploadedFile;

class ServersModel extends EntityModel
{
    public const ICON_DIR = 'uploads/servers';

    /**
     * @inheritDoc
     */
    protected $table = 'servers';

    /**
     * @inheritDoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return Server::class;
    }

    /**
     * @inheritDoc
     */
    public function find($id, bool $enabled = true)
    {
        $query = $this->createQuery()->where(
            $this->getIdColumn() . ' = ?' . ($enabled ? ' AND enabled = 1' : ''),
            $id
        );

        return is_null($row = $this->db->findOne($query)) ? null : $this->createEntity($row);
    }

    /**
     * @return Server[]
     */
    public function getEnabled()
    {
        return $this->createEntities($this->db->findAll(
            $this->createQuery()->where('enabled = 1')
        ));
    }

    /**
     * @param Server $server
     * @param UploadedFile $icon
     */
    public function uploadIcon(Server $server, UploadedFile $icon): void
    {
        $icon->move(ROOT_DIR  . '/' . static::ICON_DIR, $server->id . '.png');
    }

    /**
     * @param Server $server
     */
    public function deleteIcon(Server $server): void
    {
        $file = ROOT_DIR . '/' . static::ICON_DIR . '/' . $server->id . '.png';
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
