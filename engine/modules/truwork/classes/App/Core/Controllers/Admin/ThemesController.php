<?php


namespace App\Core\Controllers\Admin;


use App\Core\Cache\Cache;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class ThemesController extends AdminController
{
    /**
     * @return void
     */
    public function index()
    {
        $themes = [];
        foreach ($this->app->getModules() as $module)
        {
            $themes[$module->getId()] = [
                'name' => $module->getName(),
                'theme' => $module->getTheme() ?: '',
                'themes' => $module->getThemes()
            ];
        }

        $this->createView('Настройки тем модулей')
            ->addBreadcrumb('Настройки тем модулей')
            ->render('core/themes', [
                'themes' => $themes
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
            Validator::key('themes', Validator::arrayType())
        );

        $themes = $request->post('themes');
        foreach ($this->app->getModules() as $module)
        {
            $oldTheme = $module->getTheme();
            if (array_key_exists($module->getId(), $themes)) {
                $theme = htmlspecialchars($themes[$module->getId()]);
                if ($module->hasTheme($theme)) {
                    $module->setTheme($theme);
                } else {
                    $module->setTheme(null);
                }
            } else {
                $module->setTheme(null);
            }

            if ($oldTheme !== $module->getTheme()) {
                $this->updateModule($module);
            }
        }

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Темы были обновлены.')
            ->withBack(admin_url('core', 'themes'))
            ->render();
    }
}
