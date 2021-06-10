<?php


namespace App\Core\VK\Events;


class Event
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $groupId;

    /**
     * Event constructor.
     *
     * @param array $data
     * @param int $groupId
     */
    public function __construct(array $data, int $groupId)
    {
        $this->data = $data;
        $this->groupId = $groupId;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }
}
