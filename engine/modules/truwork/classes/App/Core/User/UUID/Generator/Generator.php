<?php


namespace App\Core\User\UUID\Generator;


interface Generator
{
    /**
     * @param \App\Core\Entity\User|string $user
     * @return string
     */
    public function generate($user): string;
}
