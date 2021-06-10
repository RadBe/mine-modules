<?php


namespace App\Cabinet;


use App\Cabinet\Events\BuyGroupEvent;
use App\Cabinet\Events\ExchangeMoneyEvent;
use App\Cabinet\Events\PermissionsBuyEvent;
use App\Cabinet\Events\PrefixSetEvent;
use App\Cabinet\Events\SkinCloakDeleteEvent;
use App\Cabinet\Events\SkinCloakUploadEvent;
use App\Cabinet\Events\TransferMoneyEvent;
use App\Cabinet\Events\UnbanEvent;
use App\Cabinet\Listeners\PromoListener;
use App\Cabinet\Listeners\VoteListener;
use App\Cabinet\Listeners\VotesMonthResultListener;
use App\Cabinet\Services\Payment\Payers\Pool;
use App\Cabinet\Services\UserGroupService;
use App\Core\Application;
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
        Application::getInstance()->singleton(UserGroupService::class);

        EventManager::registerLog(PermissionsBuyEvent::class);
        EventManager::registerLog(BuyGroupEvent::class);
        EventManager::registerLog(PrefixSetEvent::class);
        EventManager::registerLog(SkinCloakUploadEvent::class);
        EventManager::registerLog(SkinCloakDeleteEvent::class);
        EventManager::registerLog(UnbanEvent::class);
        EventManager::registerLog(TransferMoneyEvent::class);
        EventManager::registerLog(ExchangeMoneyEvent::class);
        EventManager::register('App\TopVotes\Events\VoteEvent', VoteListener::class);
        EventManager::register('App\TopVotes\Events\MonthResultEvent', VotesMonthResultListener::class);
        EventManager::register(PromoActivateEvent::class, PromoListener::class);

        $this->registerPayers();
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return void
     */
    protected function registerPayers(): void
    {
        Application::getInstance()->bind(Pool::class, function (Application $app) {
            $payers = [];
            foreach ($this->getConfig()->getPayersArray() as $payerName => $data)
            {
                $class = '\App\Cabinet\Services\Payment\Payers\\' . ucfirst($payerName) . '\Payer';
                $payers[] = $app->make($class, $data);
            }

            return new Pool($payers);
        });
    }
}
