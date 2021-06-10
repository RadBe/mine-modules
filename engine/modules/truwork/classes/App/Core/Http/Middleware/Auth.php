<?php


namespace App\Core\Http\Middleware;


use App\Core\Exceptions\Exception;
use App\Core\Http\Request;

class Auth extends Middleware
{
    /**
     * @param Request $request
     * @param bool|null $need
     * @throws Exception
     */
    public function handle(Request $request, ?bool $need = true)
    {
        if (is_null($need)) {
            return;
        }

        if ($need) {
            if (is_null($request->user())) {
                throw new Exception('Вы должны авторизоваться.');
            }
        } else {
            if (!is_null($request->user())) {
                throw new Exception('Доступно только для неавторизованных пользователей.');
            }
        }
    }
}
