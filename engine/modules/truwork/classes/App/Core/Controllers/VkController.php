<?php


namespace App\Core\Controllers;


use App\Core\Application;
use App\Core\Exceptions\ClassNotFoundException;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\VK\EventManager;
use Respect\Validation\Validator;

class VkController extends Controller
{
    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\ClassNotFoundException
     */
    public function call(Request $request): void
    {
        if (!Application::$ajaxMode) {
            abort('Неправильный метод вызова!', 403);
        }

        $request->validate(
            Validator::key('type', Validator::stringType())
                ->key('object', Validator::arrayType(), false)
                ->key('group_id', Validator::numericVal())
                ->key('secret', Validator::stringType())
        );

        if ($request->post('group_id') != $this->getModule()->getConfig()->getVKGroupId()) {
            abort('ID группы не совпадает с конфигом', 403);
        }

        if ($request->post('secret') != $this->getModule()->getConfig()->getVKSecretKey()) {
            abort('Неправильный ключ!', 403);
        }

        if ($request->post('type') == 'confirmation') {
            die($this->getModule()->getConfig()->getVKConfirmationKey());
        }

        /* @var EventManager $manager */
        $manager = $this->app->make(EventManager::class);
        try {
            $manager->callEvent(
                $request->post('type'),
                $request->post('object', []),
                (int) $request->post('group_id')
            );
        } catch (ClassNotFoundException $e) {
            file_put_contents(__DIR__ . '/err.txt', $e->getMessage() . PHP_EOL . htmlspecialchars(var_export($request->post(), true)));
        }
        die('ok');
    }
}
