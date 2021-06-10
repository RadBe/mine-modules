<?php


namespace App\Promo\Controllers;


use App\Core\Application;
use App\Core\Exceptions\Exception;
use App\Core\Http\Controller;
use App\Core\Http\Middleware\Auth;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedUser;
use App\Core\View\Alert;
use App\Promo\Entity\Promo;
use App\Promo\Events\PromoActivateEvent;
use App\Promo\Models\PromoModel;
use App\Promo\Module;
use Respect\Validation\Validator;

class IndexController extends Controller
{
    use NeedUser;

    /**
     * @var PromoModel
     */
    private $promoModel;

    /**
     * PromoController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws \App\Core\Exceptions\Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->promoModel = $this->app->make(PromoModel::class);
        $this->middleware(Auth::class);
    }

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $view = $this->createView('promo/index.tpl', ['csrf' => tw_csrf(true)])
            ->if('bootstrap', $this->app->getConfig()->useBootstrap());

        if (!empty($request->post('code'))) {
            try {
                $promo = $this->activate($request);
                $view->addAlert(
                    new Alert(true, "Промо-код {$promo->code} действителен! Вы получаете: {$promo->amount} руб.")
                );
            } catch (Exception $exception) {
                $view->addAlert(new Alert(false, $exception->getMessage()));
            }
        }

        $view->compile();
    }

    /**
     * @param Request $request
     * @return Promo
     * @throws Exception
     */
    protected function activate(Request $request): Promo
    {
        $request->checkCsrf();
        $request->validate(Validator::key('code', Validator::stringType()->length(1)));

        $promo = $this->getPromo($request->post('code'));
        $this->promoModel->delete($promo);
        $request->user()->depositMoney($promo->amount);

        $this->getUserModel()->updateBalance($request->user());

        dispatch(new PromoActivateEvent($request->user(), $promo));

        return $promo;
    }

    /**
     * @param string $code
     * @return Promo
     * @throws \App\Core\Exceptions\Exception
     */
    private function getPromo(string $code): Promo
    {
        return optional($this->promoModel->find($code))
            ->getOrFail('Промо-код не найден!');
    }
}
