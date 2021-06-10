<?php


namespace App\Core\Http;


use App\Core\Application;
use App\Core\Cache\Cache;
use App\Core\Entity\Module;
use App\Core\Exceptions\Exception;
use App\Core\Http\Traits\HasMiddleware;
use App\Core\Support\Optional;
use App\Core\User\Session;
use App\Core\View\Alert;
use App\Core\View\Content;
use App\Core\View\View;

abstract class Controller
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
     * @var \App\Core\View\Meta
     */
    protected $meta;

    /**
     * @var Alert[]
     */
    protected $alerts = [];

    /**
     * Controller constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        $this->app = $app;
        $this->module = $module;
        $this->action = $action;
        $this->meta = $app->make('meta');
        if (is_null($this->meta)) {
            $this->meta = new Optional(null);
        }

        if (!$module->isInstalled() || !$module->isEnabled()) {
            throw new Exception('Модуль не установлен.');
        }

        $this->loadSessionAlert();
    }

    /**
     * @return void
     */
    private function loadSessionAlert(): void
    {
        $alert = Session::getAndForget('tw_alert');
        if (!empty($alert) && is_array($alert)) {
            $this->alerts[] = new Alert($alert['status'] ?? false, $alert['message'] ?? '');
        }
    }

    /**
     * @param string $path
     * @param array $data
     * @return Content
     */
    protected function createView(string $path, array $data = []): Content
    {
        $view = new Content($path, $data);
        if (!empty($this->alerts)) {
            foreach ($this->alerts as $alert)
            {
                $view->addAlert($alert);
            }
        }

        if (!empty($this->module->getTheme())) {
            $view->setTheme($this->module->getTheme());
        }
        return $view;
    }

    /**
     * @param string $path
     * @param array $data
     */
    protected function compile(string $path, array $data = []): void
    {
        $this->createView($path, $data)->compile();
    }

    /**
     * @param string $key
     * @param \Closure $callback
     * @param int $seconds
     */
    protected function compileWithCache(string $key, \Closure $callback, int $seconds = 0): void
    {
        $this->tpl()->result[View::$CONTENT_NAME] =
            Cache::remember('tw_view_' . $key, function () use ($callback) {
                call_user_func($callback);
                return $this->tpl()->result[View::$CONTENT_NAME];
            }, $seconds);
    }

    /**
     * @return void
     */
    protected function printTpl(): void
    {
        print $this->tpl()->result[View::$CONTENT_NAME];
        die;
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
     * @param array $data
     */
    protected function printJsonData(array $data)
    {
        header('Content-Type: application/json');
        print json_encode($data);
    }

    /**
     * @param string $text
     * @throws Exception
     */
    protected function error(string $text): void
    {
        throw new Exception($text);
    }

    /**
     * @return Module
     */
    protected function getModule(): Module
    {
        return $this->module;
    }

    /**
     * @param string $url
     * @param Alert|null $alert
     */
    protected function redirect(string $url, ?Alert $alert): void
    {
        if (!is_null($alert)) {
            Session::put('tw_alert', $alert->toArray());
        }

        header('Location: ' . $url);
        die;
    }

    /**
     * @return \dle_template
     */
    private function tpl(): \dle_template
    {
        return $this->app->make('tpl');
    }
}
