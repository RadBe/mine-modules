<?php


namespace App\Cabinet\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class PrefixController extends AdminController
{
    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveSettings(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('min', Validator::intVal()->min(0))
                ->key('max', Validator::intVal()->min(0))
                ->key('regex', Validator::stringType())
                ->key('groups', Validator::stringType())
        );

        $this->module->getConfig()->setPrefixMin((int) $request->post('min'));
        $this->module->getConfig()->setPrefixMax((int) $request->post('max'));
        $this->module->getConfig()->setPrefixRegex($request->post('regex'));
        $this->module->getConfig()->setPrefixGroups(explode(',', strtolower(trim($request->post('groups', '')))));

        $this->saveConfig();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveColors(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('colors', Validator::arrayType())
        );

        $colors = [];
        foreach ($request->post('colors') as $color => $v)
        {
            $colors[] = str_replace('&', '', $color);
        }

        $colors = $this->app->getConfig()->getColors($colors);

        $this->module->getConfig()->setColors(array_keys($colors));
        $this->saveConfig();
    }

    /**
     * @param string $message
     */
    private function saveConfig(string $message = 'Настройки сохранены.'): void
    {
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', $message)
            ->withBack(admin_url('cabinet', 'settings', 'index', ['tab' => 'prefix']))
            ->render();
    }
}
