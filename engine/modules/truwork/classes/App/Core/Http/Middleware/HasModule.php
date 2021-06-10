<?php


namespace App\Core\Http\Middleware;


use App\Core\Exceptions\Exception;
use App\Core\Http\Request;

class HasModule extends Middleware
{
    /**
     * @param Request $request
     * @param string $moduleId
     */
    public function handle(Request $request, string $moduleId)
    {
        if (!$this->app->hasModule($moduleId)) {
            throw new Exception('Этот модуль не найден или отключен!');
        }
    }
}
