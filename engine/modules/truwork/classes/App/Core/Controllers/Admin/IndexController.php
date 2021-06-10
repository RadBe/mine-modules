<?php


namespace App\Core\Controllers\Admin;


use App\Core\Application;
use App\Core\Cache\Cache;
use App\Core\Cache\FileCache;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class IndexController extends AdminController
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $modules = $this->app->getModules();

        $this->createView('Truwork модули ' . Application::VERSION)
            ->render('main', [
                'modules' => array_values(array_filter(/*Module*/ $modules, function ($module) {
                    return $module->getId() != 'core';
                }))
            ]);
    }

    /**
     * @return void
     */
    public function clearCache()
    {
        Cache::flush();
        Cache::file()->flush();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Кэш модулей очищен.')
            ->withBack($this->homeUrl())
            ->render();
    }

    /**
     * @return void
     */
    public function clearCacheSkins()
    {
        Cache::skin()->flush();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Кэш скинов очищен.')
            ->withBack($this->homeUrl())
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function toggleModuleEnabled(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('module', Validator::stringType())
                ->key('enabled', Validator::boolVal(), false)
        );

        $module = $this->app->getModule($request->post('module'));
        if (!$module->isInstalled()) {
            $this->printJsonResponse(false, 'Ошибка!', 'Модуль не установлен!');
            die;
        }
        $module->setEnabled((bool) $request->post('enabled', false));

        $this->updateModule($module);

        $this->printJsonResponse(true, 'Успех!', sprintf('Модуль %s был %s', $module->getName(), $module->isEnabled() ? 'включен' : 'отключен'));
    }
}
