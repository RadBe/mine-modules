<?php


namespace App\TopVotes;


use App\Core\Application;
use App\Core\Entity\Module as BaseModule;
use App\Core\Events\EventManager;
use App\Core\Events\LogEvent;
use App\TopVotes\Entity\User;
use App\TopVotes\Tops\Pool;

class Module extends BaseModule
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        Application::getInstance()->bind(Pool::class, function () {
            $tops = [];
            foreach ($this->getConfig()->getTops() as $top => $data)
            {
                $tops[] = new $data['instance']($data['secret'], $data['rewards']);
            }

            return new Pool($tops);
        });
        EventManager::registerLog(LogEvent::class);
        User::setVotesColumn($this->getConfig()->getVotesColumn());
        User::setBonusesColumn($this->getConfig()->getBonusesColumn());
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return parent::getConfig();
    }
}
