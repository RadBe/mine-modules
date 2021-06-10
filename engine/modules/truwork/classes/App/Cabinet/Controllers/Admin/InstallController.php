<?php


namespace App\Cabinet\Controllers\Admin;


use App\Cabinet\Config;
use App\Cabinet\Entity\Group;
use App\Core\Http\AdminController;
use App\Core\Http\ModuleInstallation;
use App\Core\Http\Request;

class InstallController extends AdminController implements ModuleInstallation
{
    /**
     * @inheritDoc
     */
    public function index(): void
    {
        $hasBanlist = $this->app->hasModule('banlist');

        /* @var Config $config */
        $config = $this->module->getConfig();
        $config->setModuleStatus('groups', true);
        $config->setModuleStatus('other_groups', true);
        $config->setModuleStatus('prefix', true);
        $config->setModuleStatus('perms', true);
        $config->setModuleStatus('skin', true);
        $config->setModuleStatus('unban', $hasBanlist);
        $config->setModuleStatus('balance', true);
        $config->setModuleStatus('balance_transfer', true);
        $config->setModuleStatus('balance_exchange', true);

        $config->setSkinResolutions([[64, 32], [64, 64]]);
        $config->setSkinResolutions([[1024, 512]], true);
        $config->setCloakResolutions([[22, 17]]);
        $config->setSkinSize(1024);
        $config->setCloakSize(1024);

        $config->setPrefixMin(0);
        $config->setPrefixMax(6);
        $config->setPrefixRegex('A-Za-z0-9');

        $config->setPermission('hd_skin', 'Загрузка HD скина', 0, true);
        $config->setPermission('cloak', 'Загрузка плаща', 0, true);
        $config->setPermission('hd_cloak', 'Загрузка HD плаща', 0, true);
        $config->setPermission('prefix', 'Установка префикса', 0, true);

        $config->setColors(array_keys($this->app->getConfig()->getColors()));

        $config->setUnbanPrice(150);

        $testGroup = Group::create('vip');
        $testGroup->setPeriod(30, 150);
        $testGroup->setPeriod(-1, 1500);
        $config->addGroup($testGroup);

        $this->module->setInstalled(true);

        $this->updateModule();

        $this->redirect(admin_url('cabinet'));
    }

    /**
     * @inheritDoc
     */
    public function install(Request $request): void
    {
        //do nothing
    }
}
