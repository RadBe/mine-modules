<?php


namespace App\Referal\Controllers;


use App\Core\Application;
use App\Core\Entity\Module;
use App\Core\Http\Controller;
use App\Core\Http\Middleware\Auth;
use App\Core\Http\Traits\NeedUser;
use App\Core\View\View;

class IndexController extends Controller
{
    use NeedUser;

    /**
     * @inheritDoc
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->middleware(Auth::class);
    }

    /**
     * @return void
     */
    public function index()
    {
        $this->meta->setTitle('Мои рефералы');
        $referals = $this->getUserModel()->getReferals($this->app->getUser());
        $data = [];
        if (!is_null($referals)) {
            foreach ($referals as $referal)
            {
                array_push($data, [
                    'name' => $referal->name,
                    'reg_date' => date('d.m.Y H:i', $referal->reg_date),
                    'profit' => $referal->referer_bal
                ]);
            }
        }
        $referer = null;
        if (!is_null($this->app->getUser()->referer_id)) {
            $referer = $this->getUserModel()->find($this->app->getUser()->referer_id);
        }

        $this->createView('referal/index.tpl', [
            'url' => $this->app->getDLEConfig()->getHost() . ltrim('/?do=register&referer=', '/') . $this->app->getUser()->getId(),
            'referer' => is_null($referer) ? null : $referer->name,
            'referals' => empty($data) ? null : new View('referals', 'referal/referal.tpl', $data),
            'rate' => $this->module->getConfig()->getReferalRate()
        ])
            ->if('referer', !is_null($referer), true)
            ->if('referals', !empty($data))
            ->if('bootstrap', $this->app->getCore()->getConfig()->useBootstrap())
            ->compile();
    }
}
