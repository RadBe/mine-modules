<?php


namespace App\TopVotes\Tops;


use App\TopVotes\Exceptions\InvalidRequestDataException;

class TopCraft extends Top
{
    /**
     * @param array $post
     * @throws InvalidRequestDataException
     */
    public function init(array $post): void
    {
        if (!isset($post['username']) || empty(trim($post['username']))) {
            throw new InvalidRequestDataException('username');
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
     * @inheritDoc
     */
    public function name(): string
    {
        return 'topcraft';
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return trim(htmlspecialchars($this->post['username']));
    }

    /**
     * @return bool
     */
    public function checkSign(): bool
    {
        return $this->post['signature'] == sha1($this->getUsername() . $this->post['timestamp'] . $this->secret);
    }
}
