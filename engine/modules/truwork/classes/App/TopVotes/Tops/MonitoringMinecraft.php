<?php


namespace App\TopVotes\Tops;


use App\TopVotes\Exceptions\InvalidRequestDataException;

class MonitoringMinecraft extends Top
{
    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'monitoringminecraft';
    }

    /**
     * @inheritDoc
     */
    public function init(array $post): void
    {
        if (!isset($post['username']) || empty(trim($post['username']))) {
            throw new InvalidRequestDataException('username');
        }

        if (!isset($post['ip']) || empty(trim($post['ip']))) {
            throw new InvalidRequestDataException('signature');
        }

        if (!isset($post['signature']) || empty(trim($post['signature']))) {
            throw new InvalidRequestDataException('signature');
        }

        if (!isset($post['timestamp']) || empty(trim($post['timestamp']))) {
            throw new InvalidRequestDataException('timestamp');
        }

        parent::init($post);
    }

    /**
     * @return bool
     */
    public function checkSign(): bool
    {
        return sha1($this->post['username'] . $this->post['timestamp'] . $this->secret) == $this->post['signature'];
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->post['username'];
    }
}
