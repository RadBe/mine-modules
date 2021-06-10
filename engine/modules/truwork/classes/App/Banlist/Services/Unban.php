<?php


namespace App\Banlist\Services;


use App\Banlist\Entity\Ban;
use App\Banlist\Exceptions\UserNotBannedException;
use App\Banlist\Models\BansModel;
use App\Banlist\Module;
use App\Core\Application;
use App\Core\Entity\User;

class Unban
{
    /**
     * @var BansModel
     */
    protected $bansModel;

    /**
     * Unban constructor.
     *
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->bansModel = Application::getInstance()->make($module->getConfig()->getModel());
    }

    /**
     * @param User $user
     * @return Ban|null
     * @throws UserNotBannedException
     */
    public function unban(User $user): ?Ban
    {
        $ban = $this->bansModel->findByUser($user);
        if (is_null($ban)) {
            throw new UserNotBannedException($user->getName());
        }

        return $this->bansModel->unban($ban) ? $ban : null;
    }
}
