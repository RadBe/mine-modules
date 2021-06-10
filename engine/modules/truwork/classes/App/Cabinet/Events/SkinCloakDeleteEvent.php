<?php


namespace App\Cabinet\Events;


use App\Core\Entity\User;
use App\Core\Events\LogEvent;

class SkinCloakDeleteEvent extends LogEvent
{
    /**
     * @var string
     */
    public $type;

    /**
     * SkinCloakDeleteEvent constructor.
     *
     * @param User $user
     * @param string $type
     */
    public function __construct(User $user, string $type)
    {
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return sprintf('Удаление %s', $this->type == 'skin' ? 'скина' : 'плаща');
    }
}
