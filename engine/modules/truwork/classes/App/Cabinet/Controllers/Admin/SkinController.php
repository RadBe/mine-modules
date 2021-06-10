<?php


namespace App\Cabinet\Controllers\Admin;


use App\Cabinet\Config;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class SkinController extends AdminController
{
    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function save(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('skin',
                Validator::arrayType()
                    ->key('resolutions', Validator::stringType())
                    ->key('hd_resolutions', Validator::stringType())
                    ->key('size', Validator::intVal()->min(1))
                    ->key('hd_groups', Validator::stringType())
            )
                ->key('cloak',
                    Validator::arrayType()
                        ->key('resolutions', Validator::stringType())
                        ->key('hd_resolutions', Validator::stringType())
                        ->key('size', Validator::intVal()->min(1))
                        ->key('groups', Validator::stringType())
                        ->key('hd_groups', Validator::stringType())
                )
        );

        /* @var Config $config */
        $config = $this->module->getConfig();

        $skin = $request->post('skin');
        $cloak = $request->post('cloak');

        $config->setSkinResolutions($this->parseResolutions($skin, false), false);
        $config->setSkinResolutions($this->parseResolutions($skin, true), true);
        $config->setCloakResolutions($this->parseResolutions($cloak, false), false);
        $config->setCloakResolutions($this->parseResolutions($cloak, true), true);

        $config->setSkinSize((int) $skin['size']);
        $config->setCloakSize((int) $cloak['size']);

        $config->setHDSkinGroups(explode(',', strtolower(trim(($skin['hd_groups'])))));
        $config->setCloakGroups(explode(',', strtolower(trim(($cloak['groups'])))));
        $config->setHDCloakGroups(explode(',', strtolower(trim(($cloak['hd_groups'])))));

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки сохранены.')
            ->withBack(admin_url('cabinet','settings', 'index',  ['tab' => 'skins']))
            ->render();
    }

    /**
     * @param array $data
     * @param bool $hd
     * @return array
     */
    private function parseResolutions(array $data, bool $hd): array
    {
        $key = ($hd ? 'hd_' : '') . 'resolutions';
        $result = [];
        foreach (explode(',', $data[$key]) as $resolution)
        {
            $wh = explode('x', $resolution);
            if (count($wh) == 2 && is_numeric($wh[0]) && is_numeric($wh[1])) {
                array_push($result, [(int) $wh[0], (int) $wh[1]]);
            }
        }

        return $result;
    }
}
