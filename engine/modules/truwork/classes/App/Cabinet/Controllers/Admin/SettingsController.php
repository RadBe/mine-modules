<?php


namespace App\Cabinet\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class SettingsController extends AdminController
{
    use NeedServer;

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'home');

        $bootstrap = $this->app->getConfig()->useBootstrap();

        $resolutionFunc = function (array $resolution) {
            return $resolution[0] . 'x' . $resolution[1];
        };

        $skinConfig = [
            'skinResolutions' => implode(',', array_map($resolutionFunc, $this->module->getConfig()->getSkinResolutions())),
            'skinHDResolutions' => implode(',', array_map($resolutionFunc, $this->module->getConfig()->getSkinResolutions(true))),
            'skinSize' => $this->module->getConfig()->getData()['skin']['size'] ?? 1,
            'groups' => implode(',', $this->module->getConfig()->getHDSkinGroups()),
        ];
        $cloakConfig = [
            'cloakResolutions' => implode(',', array_map($resolutionFunc, $this->module->getConfig()->getCloakResolutions())),
            'cloakHDResolutions' => implode(',', array_map($resolutionFunc, $this->module->getConfig()->getCloakResolutions(true))),
            'cloakSize' => $this->module->getConfig()->getData()['cloak']['size'] ?? 1,
            'groups' => implode(',', $this->module->getConfig()->getCloakGroups()),
            'hd_groups' => implode(',', $this->module->getConfig()->getHDCloakGroups()),
        ];
        $prefixConfig = [
            'min' => $this->module->getConfig()->getPrefixMin(),
            'max' => $this->module->getConfig()->getPrefixMax(),
            'regex' => $this->module->getConfig()->getPrefixRegex(),
            'groups' => implode(',', $this->module->getConfig()->getPrefixGroups()),
            'colors' => $this->module->getConfig()->getColors()
        ];
        $unbanConfig = [
            'price' => $this->module->getConfig()->getUnbanPrice()
        ];

        $colors = $this->app->getConfig()->getColors();
        $csrfInput = tw_csrf(true);

        $perms = $this->module->getConfig()->getPermissions(false);

        $enabledModules = $this->module->getConfig()->getModules();
        $hasModuleBanlist = $this->app->hasModule('banlist');
        $hasModuleTopVotes = $this->app->hasModule('top-votes');

        $servers = $this->getServersModel()->getEnabled();
        $gameMoneyPlugins = $this->app->getConfig()->getGameMoneyManagers();
        $gameMoneyRateMoney = $this->module->getConfig()->getGameMoneyRate();

        $this->createView('Основные настройки')
            ->addBreadcrumb($this->module->getName(), admin_url('cabinet'))
            ->addBreadcrumb('Основные настройки')
            ->render(
                'cabinet/settings/index',
                compact(
                    'tab', 'bootstrap', 'colors',
                    'servers',
                    'skinConfig', 'cloakConfig',
                    'csrfInput',
                    'perms',
                    'prefixConfig',
                    'unbanConfig',
                    'enabledModules', 'hasModuleBanlist', 'hasModuleTopVotes',
                    'gameMoneyPlugins', 'gameMoneyRateMoney'
                )
            );
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function save(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('modules', Validator::arrayType(), false)
        );

        $requestModules = $request->post('modules');

        foreach ($this->module->getConfig()->getModules() as $module => $enabled)
        {
            if (isset($requestModules[$module])) {
                $this->module->getConfig()->setModuleStatus($module, (bool) $requestModules[$module]);

                unset($requestModules[$module]);
            } else {
                $this->module->getConfig()->removeModule($module);
            }
        }

        foreach ($requestModules as $module => $enabled)
        {
            $module = trim(preg_replace('/[^a-z0-9\-_]/u', '', strtolower($module)));
            if (!empty($module)) {
                $this->module->getConfig()->addModule($module, (bool) $enabled);
            }
        }

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки сохранены.')
            ->withBack(admin_url('cabinet', 'settings'))
            ->render();
    }
}
