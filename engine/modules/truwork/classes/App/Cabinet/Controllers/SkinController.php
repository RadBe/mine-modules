<?php


namespace App\Cabinet\Controllers;


use App\Cabinet\Events\SkinCloakDeleteEvent;
use App\Cabinet\Events\SkinCloakUploadEvent;
use App\Cabinet\Middleware\CheckModuleEnabled;
use App\Cabinet\Module;
use App\Core\Application;
use App\Core\Exceptions\Exception;
use App\Core\Http\Request;
use App\Core\Services\SkinManager;

class SkinController extends Controller
{
    /**
     * SkinController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->middleware(new CheckModuleEnabled($module->getConfig()), 'skin');
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function uploadSkin(Request $request)
    {
        $request->checkCsrf();

        $image = $request->image('file');
        if ($image->getMimeType() != 'image/png') {
            throw new Exception('Файл не является изображением.');
        }

        if ($image->getSize() > $this->module->getConfig()->getSkinSize()) {
            throw new Exception('Превышен вес файла.');
        }

        $perms = $this->module->getConfig()->getSkinGroups();
        $hdPerms = $this->module->getConfig()->getHDSkinGroups();
        $hasHDPerm = $this->user()->hasPermissionOrGroups(null, 'hd_skin', $hdPerms);
        $hasPerm = $hasHDPerm || $this->user()->hasPermissionOrGroups(null, 'skin', $perms);

        $dir = SkinManager::getDirectory(true) . '/skins/';

        [$width, $height] = $image->getScale();
        if ($this->checkResolution($this->module->getConfig()->getSkinResolutions(true), $width, $height)) {
            if (!$hasHDPerm) {
                throw new Exception('Недостаточно прав для загрузки HD скина!');
            }

            if (is_null($image->move($dir, $this->app->getUser()->getName() . '.png'))) {
                throw new Exception('Не удалось загрузить HD скин.');
            } else {
                SkinManager::clearCache($this->app->getUser()->getName());
                dispatch(new SkinCloakUploadEvent($request->user(), 'skin', $image, true));
                $this->printJsonResponse(true, 'Успех!', 'HD скин загружен.');
                return;
            }
        }

        if ($this->checkResolution($this->module->getConfig()->getSkinResolutions(), $width, $height)) {
            if (!$hasPerm) {
                throw new Exception('Недостаточно прав для загрузки скина!');
            }

            if (is_null($image->move($dir, $this->app->getUser()->getName() . '.png'))) {
                throw new Exception('Не удалось загрузить скин.');
            } else {
                SkinManager::clearCache($this->app->getUser()->getName());
                dispatch(new SkinCloakUploadEvent($request->user(), 'skin', $image, false));
                $this->printJsonResponse(true, 'Успех!', 'Скин загружен.');
                return;
            }
        }

        throw new Exception('Недопустимый размер скина.');
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function uploadCloak(Request $request)
    {
        $request->checkCsrf();

        $image = $request->image('file');
        if ($image->getMimeType() != 'image/png') {
            throw new Exception('Файл не является изображением.');
        }

        if ($image->getSize() > $this->module->getConfig()->getCloakSize()) {
            throw new Exception('Превышен вес файла.');
        }

        $user = $this->user();

        $perms = $this->module->getConfig()->getCloakGroups();
        $permsHD = $this->module->getConfig()->getHDCloakGroups();
        $hasHDPerm = $user->hasPermissionOrGroups(null, 'hd_cloak', $permsHD);
        $hasPerm = $hasHDPerm || $user->hasPermissionOrGroups(null, 'cloak', $perms);

        if (!$hasPerm) {
            throw new Exception('У вас нет прав на загрузку плаща.');
        }

        $dir = SkinManager::getDirectory(true) . '/cloaks/';

        $isUploaded = $isHd = false;
        [$width, $height] = $image->getScale();
        if ($hasHDPerm && $this->checkResolution($this->module->getConfig()->getCloakResolutions(true), $width, $height)) {
            if (is_null($image->move($dir, $this->app->getUser()->getName() . '.png'))) {
                throw new Exception('Не удалось загрузить плащ.');
            } else {
                $isUploaded = true;
                $isHd = true;
            }
        }

        if (!$isUploaded && $this->checkResolution($this->module->getConfig()->getCloakResolutions(), $width, $height)) {
            if (is_null($image->move($dir, $this->app->getUser()->getName() . '.png'))) {
                throw new Exception('Не удалось загрузить плащ.');
            } else {
                $isUploaded = true;
            }
        }

        if ($isUploaded) {
            SkinManager::clearCache($this->app->getUser()->getName());
            dispatch(new SkinCloakUploadEvent($request->user(), 'cloak', $image, $isHd));
            $this->printJsonResponse(true, 'Успех!', 'Плащ загружен.');
            die;
        }

        throw new Exception('Недопустимый размер плаща.');
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function deleteSkin(Request $request)
    {
        $request->checkCsrf();

        $skin = SkinManager::getSkinFile($this->app->getUser()->getName());
        if (!is_file($skin)) {
            throw new Exception('Вы еще не загружали скин.');
        }

        unlink($skin);
        SkinManager::clearCache($this->app->getUser()->getName());
        dispatch(new SkinCloakDeleteEvent($request->user(), 'skin'));

        $this->printJsonResponse(true, 'Успех!', 'Скин удален.');
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function deleteCloak(Request $request)
    {
        $request->checkCsrf();

        $cloak = SkinManager::getCloakFile($this->app->getUser()->getName());
        if (!is_file($cloak)) {
            throw new Exception('Вы еще не загружали плащ.');
        }

        unlink($cloak);
        SkinManager::clearCache($this->app->getUser()->getName());
        dispatch(new SkinCloakDeleteEvent($request->user(), 'cloak'));

        $this->printJsonResponse(true, 'Успех!', 'Плащ удален.');
    }

    /**
     * @param array $resolutions
     * @param $width
     * @param $height
     * @return bool
     */
    private function checkResolution(array $resolutions, $width, $height): bool
    {
        foreach ($resolutions as $resolution)
        {
            if ($width === $resolution[0] && $height === $resolution[1]) return true;
        }

        return false;
    }
}
