<?php


namespace App\Core;


use App\Core\Entity\Module as BaseModule;
use App\Core\Entity\User;
use App\Core\Events\EventManager;
use App\Core\Events\LogListener;
use App\Core\VK\Events\WallPostNew;
use App\Core\VK\Listeners\WallPostNewListener;

class Module extends BaseModule
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        User::setMoneyColumn($this->getConfig()->getMoneyColumn());
        Application::getInstance()->singleton(LogListener::class);

        EventManager::register(WallPostNew::class, WallPostNewListener::class);
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return parent::getConfig();
    }
}
