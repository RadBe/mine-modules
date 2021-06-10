<?php


namespace App\Promo\Controllers\Admin;


use App\Core\Application;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Support\Str;
use App\Core\View\AdminAlert;
use App\Promo\Entity\Promo;
use App\Promo\Models\PromoModel;
use App\Promo\Module;
use Respect\Validation\Validator;

class PromosController extends AdminController
{
    /**
     * @var PromoModel
     */
    private $promosModel;

    /**
     * PromosController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->promosModel = new PromoModel($app);
    }

    /**
     * @return void
     */
    public function index()
    {
        $this->createView('Управление промо-кодами')
            ->addBreadcrumb($this->module->getName(), admin_url('promo'))
            ->addBreadcrumb('Управление промо-кодами')
            ->render(
                'promo/promos',
                [
                    'promos' => $this->promosModel->getAll(true, 50)
                ]
            );
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function add(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('code', Validator::stringType()->length(0, 16), false)
                ->key('sum', Validator::intVal()->min(1))
        );

        $code = trim($request->post('code', ''));
        if (empty($code)) {
            $code = Str::random(16);
        }

        $promo = $this->promosModel->find($code);
        if (!is_null($promo)) {
            throw new Exception('Такой промо-код уже есть!');
        }

        $this->promosModel->insert(new Promo([
            'code' => $code,
            'amount' => (int) $request->post('sum')
        ]));

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Промо-код добавлен.')
            ->withBack(admin_url('promo', 'promos'))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function delete(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('code', Validator::stringType())
        );

        /* @var Promo $promo */
        $promo = optional($this->promosModel->find($request->post('code')))
            ->getOrFail('Код не найден!');

        $this->promosModel->delete($promo);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Промо-код удален.')
            ->withBack(admin_url('promo', 'promos'))
            ->render();
    }
}
