<?php


namespace App\Core\Controllers;


use App\Core\Exceptions\Exception;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Services\SkinManager;
use Respect\Validation\Validator;

class SkinController extends Controller
{
    /**
     * @param Request $request
     */
    public function view(Request $request)
    {
        switch ((int) $request->get('mode')) {
            case SkinManager::MODE_BACK: $mode = SkinManager::MODE_BACK; break;
            case SkinManager::MODE_HEAD: $mode = SkinManager::MODE_HEAD; break;
            default: $mode = SkinManager::MODE_FRONT;
        }

        $name = preg_replace('/[^A-Za-zА-Яа-я0-9\-_]/u', '', $request->get('username', 'default'));

        $skinViewer = $this->app->make(SkinManager::class, $name);
        $skinViewer->render($mode);
    }

    /**
     * @param Request $request
     */
    public function download(Request $request)
    {
        $request->validate(Validator::key('type', Validator::stringType()->in(['skin', 'cloak'])), false);

        $file = $request->get('type') == 'skin'
            ? SkinManager::getSkinFile($request->user()->getName())
            : SkinManager::getCloakFile($request->user()->getName());

        if (!is_file($file)) {
            header( "HTTP/1.1 404 File not found" );
            die;
        }

        $filename = $request->user()->getName() . '.png';

        header('Content-type: application/png', true, 200);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-length: ' . filesize($file));
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($file);
        die;
    }
}
