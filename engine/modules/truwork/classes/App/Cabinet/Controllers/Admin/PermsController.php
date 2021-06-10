<?php


namespace App\Cabinet\Controllers\Admin;


use App\Cabinet\Config;
use App\Core\Cache\Cache;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class PermsController extends AdminController
{
    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function save(Request $request)
    {
        $request->checkCsrf();

        /* @var Config $config */
        $config = $this->module->getConfig();

        $request->validate(
            Validator::key('perm', Validator::stringType()->in(array_keys($config->getPermissions(false))))
                ->key('name', Validator::stringType()->length(1, 255))
                ->key('price', Validator::intVal()->min(0))
                ->key('show', Validator::boolVal(), false)
        );

        $config->setPermission(
            $request->post('perm'),
            strip_tags($request->post('name')),
            (int) $request->post('price'),
            (bool) $request->post('show', false)
        );

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки сохранены.')
            ->withBack(admin_url('cabinet', 'settings', 'index', ['tab' => 'perms']))
            ->render();
    }
}
