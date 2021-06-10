<?php


namespace App\Core\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class LauncherController extends AdminController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->createView('Настройки лаунчера')
            ->addBreadcrumb('Настройки лаунчера')
            ->render('launcher', [
                'type' => $this->module->getConfig()->getLauncherType(),
                'key' => htmlspecialchars($this->module->getConfig()->getLauncherKey()),
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function save(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('type', Validator::stringType()->in(['sashok', 'gravit']))
                ->key('key', Validator::stringType())
        );

        $this->module->getConfig()->setLauncherType($request->post('type'));
        $this->module->getConfig()->setLauncherKey($request->post('key'));

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки лаунчера сохранены.')
            ->withBack(admin_url('core', 'launcher'))
            ->render();
    }
}
