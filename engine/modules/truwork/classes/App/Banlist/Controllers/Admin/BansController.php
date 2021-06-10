<?php


namespace App\Banlist\Controllers\Admin;


use App\Banlist\Events\UnbanEvent;
use App\Banlist\Models\BansModel;
use App\Core\Application;
use App\Core\Entity\Module;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class BansController extends AdminController
{
    /**
     * @var BansModel
     */
    private $bansModel;

    /**
     * BansController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->bansModel = $app->make($module->getConfig()->getModel());
    }

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $searchUser = preg_replace('/[^A-Za-zА-Яа-яЁё0-9_\- ]/u', '', $request->any('username', ''));

        $this->createView('Список забаненных игроков')
            ->addBreadcrumb($this->module->getName(), admin_url('banlist'))
            ->addBreadcrumb('Список забаненных игроков')
            ->render('banlist/bans', [
                'bans' => $this->bansModel->getAll(true, $this->module->getConfig()->getPerPage(), $searchUser),
                'searchUser' => htmlspecialchars($searchUser)
            ]);
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function unban(Request $request)
    {
        $request->checkCsrf();
        $request->validate(Validator::key('user', Validator::stringType()->length(1, 99)));

        $user = strip_tags($request->post('user'));

        if (!is_null($ban = $this->module->getUnbanService()->unban($user))) {
            dispatch(new UnbanEvent($request->user(), $ban));
            $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Разбан игрока', "Игрок $user был разбанен.")
                ->withBack(admin_url('banlist', 'bans'))
                ->render();
        }

        throw new Exception("Не удалось разбанить игрока $user.");
    }
}
