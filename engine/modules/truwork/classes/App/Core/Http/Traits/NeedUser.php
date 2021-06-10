<?php


namespace App\Core\Http\Traits;


use App\Core\Entity\User;
use App\Core\Exceptions\Exception;
use App\Core\Http\Request;
use App\Core\Models\UserModel;
use Respect\Validation\Validator;

trait NeedUser
{
    /**
     * @var UserModel
     */
    protected $userModel;

    /**
     * @return UserModel
     */
    public function getUserModel(): UserModel
    {
        return is_null($this->userModel)
            ? $this->userModel = new UserModel($this->app)
            : $this->userModel;
    }

    /**
     * @param Request $request
     * @param bool $throwIfNotFound
     * @return User|null
     * @throws Exception
     */
    public function getUser(Request $request, bool $throwIfNotFound = true): ?User
    {
        $request->validateAny(Validator::key('user'));

        $method = 'find';

        $user = $request->any('user');
        if (is_numeric($user)) {
            $user = (int) $user;
        } elseif (is_string($user)) {
            $method = 'findByName';
        } else {
            throw new Exception('Поле user должно быть int или string.');
        }

        if ($throwIfNotFound) {
            return optional($this->getUserModel()->{$method}($user))->getOrFail('Игрок не найден!');
        }

        return $this->getUserModel()->{$method}($user);
    }
}
