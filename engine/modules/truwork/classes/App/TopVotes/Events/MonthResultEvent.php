<?php


namespace App\TopVotes\Events;


class MonthResultEvent
{
    /**
     * @var array
     *
     * $result = [['user' => \App\TopVotes\Entity\User, 'rewards' => ['money' => 1, 'bonuses' => 1], 'votes' => 5], ...]
     */
    public $result;

    /**
     * MonthResultEvent constructor.
     *
     * @param array $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }
}
