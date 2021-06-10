<?php


namespace App\Core\Controllers;


use App\Core\Entity\User;
use App\Core\Exceptions\InvalidPasswordException;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedUser;
use Respect\Validation\Validator;

class LauncherController extends Controller
{
    use NeedUser;

    /**
     * @param Request $request
     */
    public function auth(Request $request)
    {
        $request->validateAny(
            Validator::key('username', Validator::stringType())
                ->key('password', Validator::stringType())
                ->key('apiKey', Validator::stringType())
        );

        $isGravit = $this->module->getConfig()->getLauncherType() == 'gravit';
        $apiKey = $this->module->getConfig()->getLauncherKey();
        if ($apiKey !== $request->any('apiKey')) {
            print 'Неверный ключ доступа!';
            die;
        }

        $user = $this->getUserModel()->findByName($request->any('username'));
        if (is_null($user)) {
            print 'Пользователь не найден!';
            die;
        }

        $password = $request->any('password');
        try {
            $user->checkPassword($password);
        } catch (InvalidPasswordException $e) {
            print $e->getMessage();
            die;
        }

        $response = $isGravit
            ? $this->getGravitResponse($user)
            : $this->getSashokResponse($user);

        print $response;
        die;
    }

    /**
     * @param User $user
     * @return string
     */
    private function getGravitResponse(User $user): string
    {
        return 'OK:' . $user->name . ':' . ($user->user_group === 1 ? 1 : 0);
    }

    /**
     * @param User $user
     * @return string
     */
    private function getSashokResponse(User $user): string
    {
        return 'OK:' . $user->name;
    }
}
