<?php


namespace App\TopVotes\Tops;


use App\TopVotes\Exceptions\InvalidRequestDataException;

class McTop extends Top
{
    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'mctop';
    }

    /**
     * @inheritDoc
     */
    public function init(array $post): void
    {
        if (!isset($post['nickname']) || empty(trim($post['nickname']))) {
            throw new InvalidRequestDataException('nickname');
        }

        if (!isset($post['token']) || empty(trim($post['token']))) {
            throw new InvalidRequestDataException('token');
        }

        parent::init($post);
    }

    /**
     * @return bool
     */
    public function checkSign(): bool
    {
        return md5($this->post['nickname'] . $this->secret) == $this->post['token'];
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->post['nickname'];
    }
}
