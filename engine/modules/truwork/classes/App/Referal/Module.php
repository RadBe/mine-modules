<?php


namespace App\Referal;


use App\Cabinet\Events\PaymentEvent;
use App\Core\Entity\Module as BaseModule;
use App\Core\Events\EventManager;
use App\Referal\Listeners\PaymentListener;

class Module extends BaseModule
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        parent::register();

        EventManager::register(PaymentEvent::class, PaymentListener::class);
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return parent::getConfig();
    }
}
