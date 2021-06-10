<?php


namespace App\Banlist\Entity;


use DateTime;

class FigAdmin extends Ban
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
        return 'FigAdmin';
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
        return new DateTime($this->attributes['time']);
    }

    /**
     * @inheritDoc
     */
    public function getExpiry(): ?DateTime
    {
        return $this->attributes[$this->getExpiryColumn()] == $this->getPermanentFormat() || !$this->attributes[$this->getExpiryColumn()]
            ? null
            : $this->createDateTime($this->attributes[$this->getExpiryColumn()], true);
    }

    /**
     * @inheritDoc
     */
    public function getNowFormat()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @inheritDoc
     */
    public function getPermanentFormat()
    {
        return '0000-00-00 00:00:00';
    }
}
