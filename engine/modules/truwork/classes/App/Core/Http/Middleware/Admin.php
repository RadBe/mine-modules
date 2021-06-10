<?php


namespace App\Core\Http\Middleware;


use App\Core\Exceptions\Exception;
use App\Core\Http\Request;

class Admin extends Middleware
{
    /**
     * @param Request $request
     * @throws Exception
     */
    public function handle(Request $request)
    {
        if (is_null($request->user()) || $request->user()->user_group != 1) {
            throw new Exception('Вы не являетесь администратором сайта.');
        }
    }
}
