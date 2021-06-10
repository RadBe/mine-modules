<?php


namespace App\Core\Http;


use App\Core\Application;
use App\Core\Entity\Module;
use App\Core\Exceptions\Exception;
use App\Core\Exceptions\ModuleNotFoundException;
use App\Core\Support\Str;
use App\Core\View\AdminAlert;
use App\Core\View\View;
use Respect\Validation\Exceptions\ValidationException;

class Router
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var Module|null
     */
    protected $module;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * Router constructor.
     *
     * @param Application $application
     * @param string $module
     * @param string $controller
     * @param string $action
     */
    public function __construct(Application $application, string $module, string $controller, string $action)
    {
        $this->app = $application;
        if (is_null($module = $this->app->getModule($module))) {
            $this->module = null;
        } else {
            $this->module = $module;
        }
        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * @return string
     */
    protected function getModuleName(): string
    {
        return Str::studly($this->module->getId());
    }

    /**
     * @return string
     */
    protected function getControllerName(): string
    {
        return Str::studly(preg_replace('/[^a-zA-Z0-9\-+]/u', '', $this->controller));
    }

    /**
     * @return string
     */
    protected function getActionName(): string
    {
        return lcfirst(Str::studly(preg_replace('/[^a-zA-Z0-9\-+]/u', '', $this->action)));
    }

    /**
     * @return string
     */
    protected function getControllerClass(): string
    {
        $path = (!empty($this->namespace) ? $this->namespace . '/' : 'App/') .
            $this->getModuleName() . '/Controllers/' . $this->getControllerName() . 'Controller';

        return str_replace('/', '\\', $path);
    }

    /**
     * @param string $action
     */
    protected function runMiddlewares(string $action): void
    {
        /* @var \App\Core\Http\Middleware\Middleware $middleware */
        foreach ($this->app->getMiddlewares() as [$middleware, $middlewareParams])
        {
            if ($middleware->hasOnly($action) || !$middleware->hasExcept($action)) {
                $middleware->handle($this->app->getRequest(), ...$middlewareParams);
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function resolveAndCallRoute(): void
    {
        if (is_null($this->module)) {
            throw new ModuleNotFoundException();
        }

        $this->app->getRequest()->initRouterParams(
            $this->module,
            $this->getControllerName(),
            $this->getActionName()
        );

        $controllerClass = $this->getControllerClass();
        if (class_exists($controllerClass)) {

            $controller = new $controllerClass($this->app, $this->module, $action = $this->getActionName());

            $this->runMiddlewares($action);

            if(method_exists($controller, $action)) {
                call_user_func_array([$controller, $action], [$this->app->getRequest()]);
            } else {
                throw new Exception('Действие "' . $action . '" контроллера "' . $controllerClass . '" не найдено!');
            }
        } else {
            throw new Exception('Контроллер "' . $controllerClass . '" не найден!');
        }
    }

    /**
     * @param string $title
     * @param string|null $message
     */
    protected function showMainException(string $title, ?string $message): void
    {
        msgbox($title, $message);
    }

    /**
     * @param string $type
     * @param string $title
     * @param string|null $message
     */
    protected function showAdminException(string $type, string $title, ?string $message): void
    {
        msg($type, $title, $message);
    }

    /**
     * @var bool $asAdmin
     * @return void
     */
    public function run(bool $asAdmin = false): void
    {
        Application::$asAdmin = $asAdmin;
        try {
            $this->resolveAndCallRoute();
        } catch (Exception $e) {
            if (Application::$ajaxMode) {
                @header('Content-Type: application/json');
                die(json_encode(['status' => false, 'title' => 'Ошибка!', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE));
            }

            if ($asAdmin) {
                $this->showAdminException(AdminAlert::MSG_TYPE_ERROR, 'Ошибка!', $e->getMessage());
            } else {
                $this->showMainException('Ошибка!', $e->getMessage());
            }
        } catch (ValidationException $e) {
            $validationErrors = '';
            foreach ($e->getMessages() as $message)
            {
                $validationErrors .= "<p>$message</p>";
            }

            if (Application::$ajaxMode) {
                @header('Content-Type: application/json');
                die(json_encode(['status' => false, 'title' => 'Ошибка валидации!', 'message' => $validationErrors], JSON_UNESCAPED_UNICODE));
            }

            if ($asAdmin) {
                $this->showAdminException(AdminAlert::MSG_TYPE_ERROR, 'Ошибка валидации!', $validationErrors);
            } else {
                $this->showMainException('Ошибка валидации!', $validationErrors);
            }
        }
    }

    /**
     * @var bool $asAdmin
     * @return void
     */
    public function runAsIncluded(bool $asAdmin = false): void
    {
        $this->run($asAdmin);
        print $this->app->make('tpl')->result[View::$CONTENT_NAME];
    }
}
