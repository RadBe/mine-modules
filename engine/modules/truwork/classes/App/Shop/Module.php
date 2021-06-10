<?php


namespace App\Shop;


use App\Core\Entity\Module as BaseModule;
use App\Core\Events\EventManager;
use App\Shop\Events\BuyProductEvent;
use App\Shop\Models\ProductModel;

class Module extends BaseModule
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        parent::register();

        EventManager::registerLog(BuyProductEvent::class);
        ProductModel::$LIMIT = $this->getConfig()->getLimit();
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
