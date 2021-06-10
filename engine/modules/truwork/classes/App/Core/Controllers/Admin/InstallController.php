<?php


namespace App\Core\Controllers\Admin;


use App\Core\Application;
use App\Core\Cache\Cache;
use App\Core\Config;
use App\Core\Database\DB;
use App\Core\Entity\Module;
use App\Core\Exceptions\DatabaseException;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\ModuleInstallation;
use App\Core\Http\Request;
use App\Core\Services\SkinManager;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class InstallController extends AdminController implements ModuleInstallation
{
    /**
     * @var Config
     */
    private $config;

    /**
     * InstallController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->config = $this->module->getConfig();
    }

    /**
     * @inheritDoc
     */
    public function index(): void
    {
        $this->createView("Установка модуля '{$this->module->getName()}'")
            ->addBreadcrumb("Установка модуля '{$this->module->getName()}'")
            ->render('install', [
                'permissionsPlugins' => $this->config->getPermissionsManagers(),
                'uuidGenerators' => $this->config->getUUIDGenerators(),
            ]);
    }

    /**
     * @inheritDoc
     */
    public function install(Request $request): void
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('money_column', Validator::stringType()->length(1, 40)->regex('/^[A-Za-z0-9\_]+$/'))
                ->key('create_column', Validator::boolVal(), false)
                ->key('permissions_manager', Validator::stringType())
                ->key('uuid_generator', Validator::stringType())
                ->key('bootstrap', Validator::boolVal(), false)
        );

        $column = trim($request->post('money_column'));

        $plugin = $request->post('permissions_manager');
        if (!class_exists($plugin)) {
            throw new Exception('Выбранный плагин на права не найден!');
        }

        $uuidGenerator = $request->post('uuid_generator');
        if (!class_exists($uuidGenerator)) {
            throw new Exception('Выбранный способ генерации uuid не найден!');
        }

        $this->config->setMoneyColumn($column);
        $this->config->setPermissionsManagerClass($plugin);
        $this->config->setUUIDGenerator($uuidGenerator);
        $this->config->setUseBootstrap((bool) $request->post('bootstrap', false));

        $this->module->setInstalled(true);
        $this->updateModule();

        Cache::forget('truwork_modules');

        if ((bool) $request->post('create_column', false)) {
            DB::$displayErrors = false;
            try {
                $sql = 'ALTER TABLE `' . PREFIX . '_users` ADD `' . $column . '` INT NOT NULL DEFAULT \'0\';';
                if (!$this->app->getBaseDBConnection()->execute($sql)) {
                    throw new DatabaseException(0, 'Не удалось добавить колонку', $sql);
                }
            } catch (DatabaseException $exception) {
                $this->createAlert(
                    AdminAlert::MSG_TYPE_WARNING,
                    'Установка не завершена!',
                    'Не удалось добавить колонку в таблицу. Но вы можете сделать это вручную с помощью SQL запроса: ' .
                    'ALTER TABLE `' . PREFIX . '_users` ADD `' . $column . '` INT NOT NULL DEFAULT \'0\';'
                )->render();
            }
            DB::$displayErrors = true;
        }

        $skinsDir = SkinManager::getDirectory(true);
        if (!is_dir($skinsDir . '/skins')) {
            mkdir($skinsDir . '/skins', 0777, true);
        }
        if (!is_dir($skinsDir . '/cloaks')) {
            mkdir($skinsDir . '/cloaks', 0777, true);
        }

        $defaultSkin = $request->image('skin');
        if (!is_null($defaultSkin)) {
            $defaultSkin->validate(Validator::mimetype('image/png'));
            if (empty($defaultSkin->move($skinsDir . '/skins', 'default.png'))) {
                $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Предупреждение!', 'Не удалось сохранить картинку дефолтного скина.')
                    ->withBack(admin_url('core', 'settings'))
                    ->render();
            }

            SkinManager::clearCache('default');
        }

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Установка завершена.', 'Модуль был установлен')
            ->withBack(admin_url('core'))
            ->render();
    }
}
