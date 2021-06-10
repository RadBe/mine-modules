<?php


namespace App\Core\Http;


use App\Core\Application;
use App\Core\Entity\Module;
use App\Core\Entity\User;
use App\Core\Exceptions\CsrfException;
use App\Core\Support\Time;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class Request
{
    public static $PAGE_KEY = 'page';

    /**
     * @var array
     */
    private $get = [];

    /**
     * @var array
     */
    private $post = [];

    /**
     * @var array
     */
    private $files;

    /**
     * @var array
     */
    private $cookie;

    /**
     * @var array
     */
    private $server;

    /**
     * @var string
     */
    private $method = 'get';

    /**
     * @var array
     */
    private $routerParams = [
        'module' => null,
        'controller' => null,
        'action' => null,
    ];

    /**
     * Request constructor.
     */
    public function __construct()
    {
        if(isset($_SERVER['REQUEST_METHOD'])) {
            $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        }

        $this->get = $_GET ?? [];
        $this->initPost();
        $this->files = $_FILES ?? [];
        $this->cookie = $_COOKIE ?? [];
        $this->server = $_SERVER ?? [];
        $this->initCsrf();
    }

    /**
     * @return void
     */
    private function initPost(): void
    {
        if($this->method == 'post') {
            $this->post = !empty($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);
        }
    }

    /**
     * @return void
     */
    private function initCsrf(): void
    {
        if (!isset($_SESSION) || !isset($_SESSION['tw_csrf'])) {
            $_SESSION['tw_csrf'] = md5('truwork' . Time::now()->getTimestamp() . rand(-99999, 99999));
        }
    }

    /**
     * @param Module $module
     * @param string $controller
     * @param string $action
     */
    public function initRouterParams(Module $module, string $controller, string $action): void
    {
        $this->routerParams = compact('module', 'controller', 'action');
    }

    /**
     * @return array
     */
    public function getRouterParams(): array
    {
        return $this->routerParams;
    }

    /**
     * @return string
     */
    public function getCsrfToken(): string
    {
        return $_SESSION['tw_csrf'];
    }

    /**
     * @throws CsrfException
     */
    public function checkCsrf(): void
    {
        if ($this->post('tw_csrf') !== $this->getCsrfToken()) {
            throw new CsrfException();
        }
    }

    /**
     * @param string|null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get(?string $key = null, $default = null)
    {
        if(is_null($key)) {
            return $this->get;
        }

        return $this->get[$key] ?? $default;
    }

    /**
     * @param string|null $key
     * @param $value
     */
    public function setGet(?string $key, $value): void
    {
        if (is_null($key)) {
            $this->get = $value;
        } else {
            $this->get[$key] = $value;
        }
    }

    /**
     * @param string|null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function post(?string $key = null, $default = null)
    {
        if(is_null($key)) {
            return $this->post;
        }

        return $this->post[$key] ?? $default;
    }

    /**
     * @param string|null $key
     * @param $value
     */
    public function setPost(?string $key, $value): void
    {
        if (is_null($key)) {
            $this->post = $value;
        } else {
            $this->post[$key] = $value;
        }
    }

    /**
     * @param string|null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function any(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return array_merge($this->get, $this->post);
        }

        return $this->post($key, $this->get($key, $default));
    }

    /**
     * @param string $name
     * @return UploadedFile|null
     */
    public function file(string $name): ?UploadedFile
    {
        if (isset($this->files[$name])) {
            $file = new UploadedFile($this->files[$name]);
            if ($file->isValid()) {
                return $file;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return UploadedImage|null
     */
    public function image(string $name): ?UploadedImage
    {
        if (isset($this->files[$name])) {
            $file = new UploadedImage($this->files[$name]);
            if ($file->isValid()) {
                return $file;
            }
        }

        return null;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function cookie(string $key, $default = null)
    {
        return $this->cookie[$key] ?? $default;
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        return $this->server;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function header(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        $page = (int) $this->get(static::$PAGE_KEY, $this->post(static::$PAGE_KEY, 1));
        if ($page < 1) {
            $page = 1;
        }

        return $page;
    }

    /**
     * @return User|null
     */
    public function user(): ?User
    {
        return Application::getInstance()->getUser();
    }

    /**
     * @param $name
     * @return array|mixed|null
     */
    public function __get($name)
    {
        return $this->any($name);
    }

    /**
     * @param Validator $rules
     * @param bool $post
     * @throws ValidationException
     */
    public function validate(Validator $rules, bool $post = true): void
    {
        $rules->assert(($post ? array_merge($this->post, $this->files) : $this->get));
    }

    /**
     * @param Validator $rules
     */
    public function validateAny(Validator $rules): void
    {
        $rules->assert($this->any());
    }
}
