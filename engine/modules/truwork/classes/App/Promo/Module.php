<?php


namespace App\Promo;


use App\Core\Entity\Module as BaseModule;
use App\Core\Events\EventManager;
use App\Promo\Events\PromoActivateEvent;

class Module extends BaseModule
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        EventManager::registerLog(PromoActivateEvent::class);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
