<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Entity\UserGroup;
use App\Cabinet\Services\Payment\Payers\Pool;
use App\Core\Entity\Server;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Services\SkinManager;

class IndexController extends Controller
{
    use NeedServer;

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $this->meta->setTitle('Кабинет игрока ' . $request->user()->name);

        $skinCloakPath = SkinManager::getDirectory(false) . '/%type%s/%name%.png';
        $skin = SkinManager::getSkinFile($this->app->getUser()->getName());
        $cloak = SkinManager::getCloakFile($this->app->getUser()->getName());
        $skinResolutions = [
            'skin' => $this->getModule()->getConfig()->getSkinResolutions(false),
            'hd_skin' => $this->getModule()->getConfig()->getSkinResolutions(true),
            'cloak' => $this->getModule()->getConfig()->getCloakResolutions(false),
            'hd_cloak' => $this->getModule()->getConfig()->getCloakResolutions(true),
        ];

        $payers = [];
        foreach ($this->app->make(Pool::class)->all() as $payer)
        {
            if ($payer->isEnabled()) {
                $payers[] = $payer->name();
            }
        }

        $hasBanlist = !is_null($banlist = $this->app->getModule('banlist')) && $banlist->isInstalled();
        $ban = $this->getBan();

        $this->createView('cabinet/index.tpl', [
            'login' => $this->app->getUser()->getName(),
            'user' => htmlspecialchars(json_encode([
                'id' => $this->app->getUser()->getId(),
                'name' => $this->app->getUser()->getName(),
                'money' => $this->app->getUser()->getMoney(),
                'perms' => $this->app->getUser()->getPermissions(),
                'groups' => array_map(function (UserGroup $userGroup) {
                    return $userGroup->toArray();
                }, $this->user()->getGroupManager()->getGroups()),

                'skin_2d_front' => base_url('core', 'skin', 'view', [
                    'username' => $this->app->getUser()->getName(),
                    'mode' => SkinManager::MODE_FRONT
                ]),
                'skin_2d_back' => base_url('core', 'skin', 'view', [
                    'username' => $this->app->getUser()->getName(),
                    'mode' => SkinManager::MODE_BACK
                ]),

                'has_skin' => is_file($skin),
                'has_cloak' => is_file($cloak),

                'ban' => $ban,
            ])),
            'servers' => htmlspecialchars(json_encode(array_map(function (Server $server) {
                return array_merge($server->toArray(), ['img' => Server::getIcon($server)]);
            }, $this->getServersModel()->getEnabled()))),
            'settings' => htmlspecialchars(json_encode([
                'skin_cloak_path' => $skinCloakPath,
                'skin_resolutions' => $skinResolutions,
                'groups' => $this->getModule()->getConfig()->getGroupsArray(),
                'has_banlist' => $hasBanlist,
                'url' => [
                    'balance_payment' => ajax_url('cabinet', 'payment', 'redirect-payer', [
                        'username' => $this->app->getUser()->getName()
                    ]),
                    'balance_transfer' => ajax_url('cabinet', 'balance', 'transfer'),
                    'balance_exchange' => ajax_url('cabinet', 'balance', 'exchange'),
                    'payment_history' => ajax_url('cabinet', 'payment', 'history'),
                    'skin_upload' => ajax_url('cabinet', 'skin', 'upload-skin'),
                    'cloak_upload' => ajax_url('cabinet', 'skin', 'upload-cloak'),
                    'skin_delete' => ajax_url('cabinet', 'skin', 'delete-skin'),
                    'cloak_delete' => ajax_url('cabinet', 'skin', 'delete-cloak'),
                    'skin_download' => base_url('core', 'skin', 'download', ['type' => 'skin']),
                    'buy_group' => ajax_url('cabinet', 'groups', 'buy'),
                    'unban' => $hasBanlist ? ajax_url('cabinet', 'unban', 'unban') : '',
                    'buy_perm' => ajax_url('cabinet', 'permissions', 'buy'),
                    'prefix_load' => ajax_url('cabinet', 'prefix', 'load'),
                    'prefix_save' => ajax_url('cabinet', 'prefix', 'save'),
                    'logs' => ajax_url('cabinet', 'logs', 'search'),
                ],
                'unban_price' => $hasBanlist ? $this->getModule()->getConfig()->getUnbanPrice() : 0,
                'payers' => $payers,
                'perms' => $this->getModule()->getConfig()->getPermissions(),
                'group_perms' => [
                    'skin' => $this->getModule()->getConfig()->getSkinGroups(),
                    'hd_skin' => $this->getModule()->getConfig()->getHDSkinGroups(),
                    'cloak' => $this->getModule()->getConfig()->getCloakGroups(),
                    'hd_cloak' => $this->getModule()->getConfig()->getHDCloakGroups(),
                    'prefix' => $this->getModule()->getConfig()->getPrefixGroups(),
                ],
                'colors' => $this->app->getConfig()->getColors($this->getModule()->getConfig()->getColors()),
                'prefix' => [
                    'min' => $this->getModule()->getConfig()->getPrefixMin(),
                    'max' => $this->getModule()->getConfig()->getPrefixMax(),
                    'regex' => $this->getModule()->getConfig()->getPrefixRegex()
                ],
                'modules' => $this->getModule()->getConfig()->getModules(),
                'game_money_rate' => $this->getModule()->getConfig()->getGameMoneyRate(),
            ])),
            'cookieServer' => (int) $request->cookie('tw_server', -1),
            'csrf' => $request->getCsrfToken()
        ])
            ->if('bootstrap', $this->app->getConfig()->useBootstrap())
            ->compile();
    }

    /**
     * @return array|null
     */
    private function getBan(): ?array
    {
        $module = $this->app->getModule('banlist');
        if (is_null($module) || !$module->isInstalled()) return null;

        $model = $this->app->make($module->getConfig()->getModel());
        $ban = $model->findByUser($this->app->getUser());
        return is_null($ban) ? null : $ban->toArray();
    }
}
