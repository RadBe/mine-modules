<?php


namespace App\Banlist\Entity;


use App\Banlist\Models\LitebansModel;
use App\Core\Support\Time;
use DateTime;

class Litebans extends Ban
{
    public const MODEL = LitebansModel::class;

    /**
     * @inheritDoc
     */
    public function getUserColumn(): string
    {
        return 'name';
    }

    /**
     * @inheritDoc
     */
    public function getActiveColumn(): string
    {
        return 'active';
    }

    /**
     * @inheritDoc
     */
    public function getExpiryColumn(): string
    {
        return 'until';
    }

    /**
     * @inheritDoc
     */
    public function getSortColumn(): string
    {
        return 'id';
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->attributes['id'];
    }

    /**
     * @inheritDoc
     */
    public function plugin(): string
    {
        return 'Litebans';
    }

    /**
     * @inheritDoc
     */
    public function getUser(): string
    {
        return $this->attributes[$this->getUserColumn()];
    }

    /**
     * @inheritDoc
     */
    public function getReason(): string
    {
        return $this->attributes['reason'] ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getAdmin(): string
    {
        return $this->attributes['banned_by_name'];
    }

    /**
     * @inheritDoc
     */
    public function getDate(): DateTime
    {
        return $this->createDateTime((int) ($this->attributes['time'] / 1000));
    }

    /**
     * @inheritDoc
     */
    public function getExpiry(): ?DateTime
    {
        return $this->attributes[$this->getExpiryColumn()] == $this->getPermanentFormat()
            ? null
            : $this->createDateTime((int) ($this->attributes[$this->getExpiryColumn()] / 1000));
    }

    /**
     * @inheritDoc
     */
    public function getNowFormat()
    {
        return Time::now()->getTimestamp() * 1000;
    }

    /**
     * @inheritDoc
     */
    public function getPermanentFormat()
    {
        return -1;
    }
}
