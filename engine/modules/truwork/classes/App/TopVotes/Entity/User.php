<?php


namespace App\TopVotes\Entity;


use App\Core\Entity\User as BaseUser;

class User
{
    /**
     * @var string
     */
    protected static $votesColumn;

    /**
     * @var string
     */
    protected static $bonusesColumn;

    /**
     * @var BaseUser
     */
    protected $user;

    /**
     * User constructor.
     *
     * @param BaseUser $user
     */
    public function __construct(BaseUser $user)
    {
        $this->user = $user;
    }

    /**
     * @return BaseUser
     */
    public function entity(): BaseUser
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getVotes(): int
    {
        return $this->user->{static::getVotesColumn()};
    }

    /**
     * @param int $amount
     */
    public function setVotes(int $amount): void
    {
        $this->user->{static::getVotesColumn()} = $amount;
    }

    /**
     * @return int
     */
    public function getBonuses(): int
    {
        return $this->user->{static::getBonusesColumn()};
    }

    /**
     * @param int $amount
     */
    public function setBonuses(int $amount): void
    {
        $this->user->{static::getBonusesColumn()} = $amount;
    }

    /**
     * @param int $amount
     */
    public function depositBonuses(int $amount): void
    {
        $this->setBonuses($this->getBonuses() + $amount);
    }

    /**
     * @param int $amount
     */
    public function withdrawBonuses(int $amount): void
    {
        $this->setBonuses($this->getBonuses() - $amount);
    }

    /**
     * @param int $need
     * @return bool
     */
    public function hasBonuses(int $need): bool
    {
        return $this->getBonuses() >= $need;
    }

    /**
     * @return string
     */
    public static function getVotesColumn(): string
    {
        return static::$votesColumn;
    }

    /**
     * @param string $name
     */
    public static function setVotesColumn(string $name): void
    {
        static::$votesColumn = $name;
    }

    /**
     * @return string
     */
    public static function getBonusesColumn(): string
    {
        return static::$bonusesColumn;
    }

    /**
     * @param string $name
     */
    public static function setBonusesColumn(string $name): void
    {
        static::$bonusesColumn = $name;
    }

    /**
     * @param BaseUser $user
     * @return User
     */
    public static function swap(BaseUser $user): User
    {
        return new static($user);
    }
}
