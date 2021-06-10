<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Entity\User;
use App\Core\Application;
use App\Core\Entity\Module;
use App\Core\Http\Controller as BaseController;
use App\Core\Http\Middleware\Auth;

abstract class Controller extends BaseController
{
    /**
     * @var bool
     */
    protected $auth = true;

    /**
     * @inheritDoc
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        if ($this->auth) {
            $this->middleware(Auth::class);
        }
    }

    /**
     * @return User
     */
    protected function user(): User
    {
        return User::swap($this->app->getUser());
    }
}
