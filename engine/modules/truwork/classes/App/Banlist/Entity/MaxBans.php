<?php


namespace App\Banlist\Entity;


use App\Core\Support\Time;
use DateTime;

class MaxBans extends Ban
{
    public const ID_COLUMN = 'name';

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
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getExpiryColumn(): string
    {
        return 'expires';
    }

    /**
     * @inheritDoc
     */
    public function getSortColumn(): string
    {
        return 'time';
    }

    /**
     * @inheritDoc
     */
    public function plugin(): string
    {
        return 'MaxBans';
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
        return $this->attributes['banner'] ?: 'Console';
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
}
