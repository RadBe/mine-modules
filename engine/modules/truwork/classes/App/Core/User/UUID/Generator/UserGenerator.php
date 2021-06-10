<?php


namespace App\Core\User\UUID\Generator;


use App\Core\Entity\User;
use App\Core\Exceptions\Exception;

class UserGenerator implements Generator
{
    /**
     * @inheritDoc
     */
    public function generate($user): string
    {
        if (!($user instanceof User)) {
            throw new Exception('$user должел быть \App\Core\Entity\User');
        }

        return $user->uuid;
    }
}
