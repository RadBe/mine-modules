<?php


namespace App\TopVotes\Events;


use App\Core\Events\LogEvent;
use App\TopVotes\Entity\User;
use App\TopVotes\Entity\VoteLog;
use App\TopVotes\Tops\Top;

class VoteEvent extends LogEvent
{
    /**
     * @var User
     */
    public $voteUser;

    /**
     * @var Top
     */
    public $top;

    /**
     * @var VoteLog[]
     */
    public $lastVotes;

    /**
     * VoteEvent constructor.
     *
     * @param User $user
     * @param Top $top
     * @param VoteLog[] $lastVotes
     */
    public function __construct(User $user, Top $top, array $lastVotes)
    {
        $this->voteUser = $user;
        $this->user = $user->entity();
        $this->top = $top;
        $this->lastVotes = $lastVotes;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return sprintf('Голосование в топе %s', $this->top->name());
    }
}
