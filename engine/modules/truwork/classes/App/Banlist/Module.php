<?php


namespace App\Banlist;


use App\Banlist\Services\Unban;
use App\Core\Application;
use App\Core\Entity\Module as BaseModule;

class Module extends BaseModule
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        Application::getInstance()->singleton($this->getConfig()->getModel());
    }

    /**
     * @return Unban
     */
    public function getUnbanService(): Unban
    {
        return Application::getInstance()->make(Unban::class, $this);
    }
}
