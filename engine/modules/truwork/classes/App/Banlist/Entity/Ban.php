<?php


namespace App\Banlist\Entity;


use App\Banlist\Models\BansModel;
use App\Core\Entity\DatabaseEntity;
use App\Core\Support\Time;
use DateTime;

abstract class Ban extends DatabaseEntity
{
    public const MODEL = BansModel::class;

    /**
     * @return string
     */
    abstract public function plugin(): string;

    /**
     * @return string
     */
    abstract public function getUserColumn(): string;

    /**
     * @return string
     */
    abstract public function getActiveColumn(): string;

    /**
     * @return string
     */
    abstract public function getExpiryColumn(): string;

    /**
     * @return string
     */
    abstract public function getSortColumn(): string;

    /**
     * @return string
     */
    abstract public function getUser(): string;

    /**
     * @return string
     */
    abstract public function getReason(): string;

    /**
     * @return string
     */
    abstract public function getAdmin(): string;

    /**
     * @return DateTime
     */
    abstract public function getDate(): DateTime;

    /**
     * @return DateTime|null
     */
    abstract public function getExpiry(): ?DateTime;

    /**
     * @param int|string $time
     * @param bool $fromString
     * @return DateTime
     */
    protected function createDateTime($time, bool $fromString = false): DateTime
    {
        if ($fromString) {
            return new DateTime($time);
        }

        $date = new DateTime();
        $date->setTimestamp($time);

        return $date;
    }

    /**
     * @return bool
     */
    public function hasActiveColumn(): bool
    {
        return !empty($this->getActiveColumn());
    }

    /**
     * @return int|string
     */
    public function getNowFormat()
    {
        return Time::now()->getTimestamp();
    }

    /**
     * @return int|string|null
     */
    public function getPermanentFormat()
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $expiry = $this->getExpiry();

        return [
            'name' => $this->getUser(),
            'admin' => $this->getAdmin(),
            'reason' => $this->getReason(),
            'date' => $this->getDate()->format('d.m.Y H:i'),
            'expiry' => is_null($expiry) ? 0 : $expiry->getTimestamp(),
            'expiry_format' => is_null($expiry) ? '<span class="banlist-permanent">перманентно</span>' : $expiry->format('d.m.Y H:i')
        ];
    }
}
