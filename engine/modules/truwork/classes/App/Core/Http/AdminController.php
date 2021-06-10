<?php


namespace App\Core\Http;


use App\Core\Application;
use App\Core\Cache\Cache;
use App\Core\Entity\Module;
use App\Core\Exceptions\Exception;
use App\Core\Http\Middleware\Admin;
use App\Core\Http\Traits\HasMiddleware;
use App\Core\Models\ModulesModel;
use App\Core\Support\URL;
use App\Core\View\AdminAlert;
use App\Core\View\AdminView;

abstract class AdminController
{
    use HasMiddleware;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Module
     */
    protected $module;

    /**
     * @var string
     */
    protected $action;

    /**
     * AdminController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        if (!Application::$asAdmin) {
            throw new Exception('Доступ запрещен!');
        }

        $this->app = $app;
        $this->module = $module;
        $this->action = $action;

        $this->checkModuleIsInstalled($module, !($this instanceof ModuleInstallation));
        $this->middleware(Admin::class);
        if (!($this instanceof ModuleInstallation) && $action != 'toggleModuleEnabled' && !$module->isEnabled()) {
            $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Модуль не включен!', 'Модуль не включен!')
                ->withBack(admin_url('core'))
                ->render();
        }
    }

    /**
     * @param Module $module
     * @param bool $need
     */
    protected function checkModuleIsInstalled(Module $module, bool $need = true): void
    {
        if ($need && !$module->isInstalled()) {
            header('Location: /admin.php' . admin_url($module->getId(), 'install'));
            die;
        } elseif (!$need && $module->isInstalled()) {
            $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Установка модуля.', 'Модуль уже установлен!')
                ->render();
        }
    }

    /**
     * @param string $title
     * @return AdminView
     */
    protected function createView(string $title): AdminView
    {
        return (new AdminView($title))
            ->addBreadcrumb('Truwork модули ' . Application::VERSION, $this->homeUrl());
    }

    /**
     * @param string $type
     * @param string $title
     * @param string $text
     * @return AdminAlert
     */
    protected function createAlert(string $type, string $title, string $text): AdminAlert
    {
        return new AdminAlert($type, $title, $text);
    }

    /**
     * @return string
     */
    protected function homeUrl(): string
    {
        return URL::create('core');
    }

    /**
     * @param Module|null $module
     */
    protected function updateModule(?Module $module = null): void
    {
        if (is_null($module)) {
            $module = $this->module;
        }

        $this->app->make(ModulesModel::class)->update($module);

        Cache::forget('truwork_modules');
        Cache::forget('module_themes' . $module->getId());
    }

    /**
     * @param bool $status
     * @param string $title
     * @param string $message
     * @param array $data
     */
    protected function printJsonResponse(bool $status, string $title, string $message, array $data = []): void
    {
        header('Content-Type: application/json');
        print json_encode(compact('status', 'title', 'message', 'data'), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        die;
    }
}
