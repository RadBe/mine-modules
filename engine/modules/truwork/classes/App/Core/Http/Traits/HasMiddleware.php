<?php


namespace App\Core\Http\Traits;


use App\Core\Http\Middleware\Middleware;

trait HasMiddleware
{
    /**
     * @param string|Middleware $middleware
     * @param mixed ...$params
     * @return Middleware
     */
    protected function middleware($middleware, ...$params): Middleware
    {
        if (is_string($middleware)) {
            $middleware = $this->app->make($middleware);
        }

        $this->app->addMiddleware($middleware, ...$params);

        return $middleware;
    }
}
