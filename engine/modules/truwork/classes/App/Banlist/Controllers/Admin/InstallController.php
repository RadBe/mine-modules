<?php


namespace App\Banlist\Controllers\Admin;


use App\Banlist\Entity\FigAdmin;
use App\Banlist\Entity\Litebans;
use App\Banlist\Entity\MaxBans;
use App\Banlist\Entity\UltraBans;
use App\Core\Application;
use App\Core\Cache\Cache;
use App\Core\Entity\Module;
use App\Core\Http\AdminController;
use App\Core\Http\ModuleInstallation;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class InstallController extends AdminController implements ModuleInstallation
{
    /**
     * InstallController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws \App\Core\Exceptions\Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $figAdminPlugin = new FigAdmin;
        $liteBansPlugin = new Litebans;
        $maxBansPlugin = new MaxBans;
        $ultraBansPlugin = new UltraBans;

        $module->getConfig()->addPlugin($figAdminPlugin->plugin(), get_class($figAdminPlugin));
        $module->getConfig()->addPlugin($liteBansPlugin->plugin(), get_class($liteBansPlugin));
        $module->getConfig()->addPlugin($maxBansPlugin->plugin(), get_class($maxBansPlugin));
        $module->getConfig()->addPlugin($ultraBansPlugin->plugin(), get_class($ultraBansPlugin));
    }

    /**
     * @inheritDoc
     */
    public function index(): void
    {
        $this->createView("Установка модуля '{$this->module->getName()}'")
            ->addBreadcrumb("Установка модуля '{$this->module->getName()}'")
            ->render('banlist/install', [
                'module' => $this->module,
                'plugins' => $this->module->getConfig()->getPlugins(),
            ]);
    }

    /**
     * @inheritDoc
     */
    public function install(Request $request): void
    {
        $request->checkCsrf();

        $config = $this->module->getConfig();

        $request->validate(
            Validator::key('table', Validator::stringType()->length(1, 40)->regex('/^[A-Za-z0-9\_]+$/'))
                ->key('per_page', Validator::numericVal()->between(1, 1000))
                ->key('plugin', Validator::stringType()->in(array_keys($config->getPlugins())))
        );

        $config->setTable(strip_tags($request->post('table')));
        $config->setPerPage((int) $request->post('per_page'));
        $config->setPlugin($request->post('plugin'));

        $this->module->setInstalled(true);
        $this->updateModule();

        Cache::forget('truwork_modules');

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Модуль был установлен.')
            ->withBack(admin_url('banlist'))
            ->render();
    }
}
