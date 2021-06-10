<?php


namespace App\TopVotes;


use App\Core\Config\Config as BaseConfig;
use App\TopVotes\Tops\Top;

class Config extends BaseConfig
{
    public const WORDS = ['голос', 'голоса', 'голосов'];

    /**
     * @return string
     */
    public function getVotesColumn(): string
    {
        return $this->data['votes_column'] ?? 'votes';
    }

    /**
     * @param string $column
     */
    public function setVotesColumn(string $column): void
    {
        $this->data['votes_column'] = $column;
    }

    /**
     * @return string
     */
    public function getBonusesColumn(): string
    {
        return $this->data['bonuses_column'] ?? 'bonuses';
    }

    /**
     * @param string $column
     */
    public function setBonusesColumn(string $column): void
    {
        $this->data['bonuses_column'] = $column;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int) ($this->data['limit'] ?? 10);
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        if ($limit < 1) {
            $limit = 1;
        }

        $this->data['limit'] = $limit;
    }

    /**
     * @return int
     */
    public function getLimitSide(): int
    {
        return (int) ($this->data['limit_side'] ?? 5);
    }

    /**
     * @param int $limit
     */
    public function setLimitSide(int $limit): void
    {
        if ($limit < 1) {
            $limit = 1;
        }

        $this->data['limit_side'] = $limit;
    }

    /**
     * @return array
     */
    public function getMonthRewards(): array
    {
        return $this->data['month_rewards'] ?? [];
    }

    /**
     * @param array $rewards
     */
    public function addMonthRewards(array $rewards): void
    {
        if (!isset($this->data['month_rewards']) || !is_array($this->data['month_rewards'])) {
            $this->data['month_rewards'] = [];
        }

        $this->data['month_rewards'][] = $rewards;
    }

    /**
     * @param int $position
     */
    public function removeMonthRewards(int $position): void
    {
        if (isset($this->data['month_rewards'][$position])) {
            array_splice($this->data['month_rewards'], $position, 1);
        }
    }

    /**
     * @param int $position
     * @param string $reward
     * @param int $amount
     */
    public function updateMonthReward(int $position, string $reward, int $amount): void
    {
        $this->data['month_rewards'][$position][$reward] = $amount;
    }

    /**
     * @param int $position
     * @param string $reward
     */
    public function removeMonthReward(int $position, string $reward): void
    {
        if (isset($this->data['month_rewards'][$position][$reward])) {
            unset($this->data['month_rewards'][$position][$reward]);
        }
    }

    /**
     * @return array
     */
    public function getTops(): array
    {
        return $this->data['tops'];
        /*return [
            'topcraft' => [
                'instance' => TopCraft::class,
                'secret' => 'abc',
                'rewards' => [
                    'bonuses' => 5,
                    'money' => 1
                ]
            ],
            'mcrate' => [
                'instance' => TopCraft::class,
                'secret' => 'abc',
                'rewards' => [
                    'bonuses' => 2,
                    'money' => 1
                ]
            ],
        ];*/
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasTop(string $name): bool
    {
        return array_key_exists($name, $this->data['tops']);
    }

    /**
     * @param Top $top
     */
    public function saveTop(Top $top): void
    {
        $this->data['tops'][$top->name()] = [
            'instance' => get_class($top),
            'secret' => $top->getSecret(),
            'rewards' => $top->getRewards(),
        ];
    }

    /**
     * @return int
     */
    public function getBonusesGameMoneyRate(): int
    {
        return $this->data['bonuses_g_money_rate'] ?? 1;
    }

    /**
     * @param int $rate
     */
    public function setBonusesGameMoneyRate(int $rate): void
    {
        $this->data['bonuses_g_money_rate'] = $rate;
    }
}
