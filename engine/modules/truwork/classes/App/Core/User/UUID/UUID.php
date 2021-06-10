<?php


namespace App\Core\User\UUID;


use App\Core\Application;
use App\Core\Entity\User;
use App\Core\User\UUID\Generator\Generator;

class UUID
{
    /**
     * @param User|string $user
     * @return string
     */
    public static function generate($user): string
    {
        return Application::getInstance()->make(Generator::class)->generate($user);
    }
}
