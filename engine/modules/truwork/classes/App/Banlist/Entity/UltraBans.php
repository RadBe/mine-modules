<?php


namespace App\Banlist\Entity;


use DateTime;

class UltraBans extends Ban
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
        return 'temptime';
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
        return 'UltraBans';
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
        return $this->attributes['reason'];
    }

    /**
     * @inheritDoc
     */
    public function getAdmin(): string
    {
        return $this->attributes['admin'];
    }

    /**
     * @inheritDoc
     */
    public function getDate(): DateTime
    {
        return $this->createDateTime($this->attributes['time']);
    }

    /**
     * @inheritDoc
     */
    public function getExpiry(): ?DateTime
    {
        return $this->attributes[$this->getExpiryColumn()] > $this->getPermanentFormat()
            ? $this->createDateTime($this->attributes[$this->getExpiryColumn()])
            : null;
    }
}
