<?php


namespace App\TopVotes\Tops;


abstract class Top
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * @var array
     */
    protected $rewards;

    /**
     * Top constructor.
     *
     * @param string $secret
     */
    public function __construct(string $secret, array $rewards)
    {
        $this->secret = $secret;
        $this->rewards = $rewards;
    }

    /**
     * @var array
     */
    protected $post = [];

    /**
     * @param array $post
     */
    public function init(array $post): void
    {
        $this->post = $post;
    }

    /**
     * @return array
     */
    public function getRewards(): array
    {
        return $this->rewards;
    }

    /**
     * @param array $rewards
     */
    public function setRewards(array $rewards): void
    {
        $this->rewards = $rewards;
    }

    /**
     * @param string $type
     * @param int $amount
     */
    public function setReward(string $type, int $amount): void
    {
        if ($amount < 0) $amount = 0;
        $this->rewards[$type] = $amount;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    abstract public function name(): string;

    /**
     * @return string
     */
    abstract public function getUsername(): string;

    /**
     * @return bool
     */
    abstract public function checkSign(): bool;
}
