<?php


namespace App\Shop\Controllers\Admin;


use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Http\Traits\NeedUser;
use App\Core\Models\ServersModel;
use App\Core\Support\AttachRelationEntity;
use App\Core\View\AdminAlert;
use App\Shop\Entity\Product;
use App\Shop\Entity\Warehouse;
use App\Shop\Models\CategoryModel;
use App\Shop\Models\WarehouseModel;
use App\Shop\Traits\NeedCategory;
use App\Shop\Traits\NeedProduct;
use Respect\Validation\Validator;

class ProductController extends AdminController
{
    use NeedCategory, NeedProduct, NeedServer, NeedUser;

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function index(Request $request)
    {
        $name = trim($request->post('name', ''));
        $server = empty($request->post('server')) ? null : $this->getServer($request);

        $products = $this->getProductModel()->search(optional($server)->id, null, $name);
        AttachRelationEntity::make($products->getResult(), $this->app->make(CategoryModel::class), 'category_id');
        AttachRelationEntity::make($products->getResult(), $this->app->make(ServersModel::class), 'server_id');

        $view = $this->createView('Управление товарами')
            ->addBreadcrumb($this->module->getName(), admin_url('shop'));

        if ($server || !empty($name)) {
            $view
                ->addBreadcrumb('Управление товарами', admin_url('shop', 'product'))
                ->addBreadcrumb('Поиск');
        } else {
            $view->addBreadcrumb('Управление товарами');
        }

        $view->render('shop/products/index', [
            'products' => $products,
            'servers' => $this->getServersModel()->getEnabled(),
            'categories' => $this->getCategoryModel()->getEnabled(),
            'search' => [
                'server' => optional($server)->id,
                'name' => htmlspecialchars($name)
            ]
        ]);
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->createView('Добавление товара')
            ->addBreadcrumb($this->module->getName(), admin_url('shop'))
            ->addBreadcrumb('Управление товарами', admin_url('shop', 'product'))
            ->addBreadcrumb('Добавление товара')
            ->render('shop/products/add', [
                'servers' => $this->getServersModel()->getEnabled(),
                'categories' => $this->getCategoryModel()->getEnabled(),
                'enchants' => $this->module->getConfig()->getEnchants(),
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     * @throws \App\Shop\Exceptions\CategoryNotFoundException
     */
    public function create(Request $request)
    {
        $request->checkCsrf();
        $this->validate($request);

        $category = $this->getCategory($request);
        $server = empty($request->post('server')) ? null : $this->getServer($request);
        $enchants = $request->post('enchants');

        /* @var Product $product */
        $product = Product::create([
            'server_id' => $server,
            'category_id' => $category,
            'name' => htmlspecialchars($request->post('name')),
            'block_id' => htmlspecialchars($request->post('block_id')),
            'amount' => (int) $request->post('amount'),
            'price' => (int) $request->post('price')
        ]);
        $product->enabled = (bool) $request->post('enabled', false);
        $this->addEnchantsToProduct($product, $enchants);
        $this->getProductModel()->insert($product);

        $img = $request->image('icon');
        if (!is_null($img)) {
            try {
                $this->getProductModel()->uploadImage($product, $img);
            } catch (Exception $exception) {
                $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Возникла ошибка!', 'Товар был добавлен, но возникла ошибка при загрузке иконки: ' . $exception->getMessage())
                    ->withBack(admin_url('shop', 'product'))
                    ->withBack(admin_url('shop', 'product', 'edit', ['product' => $product->id]), 'Редактировать')
                    ->render();
            }
        }

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Товар добавлен.')
            ->withBack(admin_url('shop', 'product'))
            ->withBack(admin_url('shop', 'product', 'edit', ['product' => $product->id]), 'Редактировать')
            ->withBack(admin_url('shop', 'product', 'add'), 'Добавить еще')
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Shop\Exceptions\ProductNotFoundException
     */
    public function edit(Request $request)
    {
        $product = $this->getProduct($request, false);

        $this->createView('Редактирование товара #' . $product->id)
            ->addBreadcrumb($this->module->getName(), admin_url('shop'))
            ->addBreadcrumb('Управление товарами', admin_url('shop', 'product'))
            ->addBreadcrumb('Редактирование товара #' . $product->id)
            ->render('shop/products/edit', [
                'product' => $product,
                'servers' => $this->getServersModel()->getEnabled(),
                'categories' => $this->getCategoryModel()->getEnabled(),
                'enchants' => $this->module->getConfig()->getEnchants()
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     * @throws \App\Shop\Exceptions\CategoryNotFoundException
     * @throws \App\Shop\Exceptions\ProductNotFoundException
     */
    public function update(Request $request)
    {
        $request->checkCsrf();
        $product = $this->getProduct($request, false);
        $this->validate($request);

        $category = $this->getCategory($request);
        $server = empty($request->post('server')) ? null : $this->getServer($request);
        $enchants = $request->post('enchants');

        $product->fill([
            'name' => htmlspecialchars($request->post('name')),
            'block_id' => htmlspecialchars($request->post('block_id')),
            'amount' => (int) $request->post('amount'),
            'price' => (int) $request->post('price'),
            'enabled' => (bool) $request->post('enabled'),
            'category_id' => $category->id,
            'server_id' => optional($server)->id
        ]);
        $product->setEnchants([]);
        $this->addEnchantsToProduct($product, $enchants);
        $this->getProductModel()->update($product);

        $img = $request->image('icon');
        if (!is_null($img)) {
            try {
                $this->getProductModel()->uploadImage($product, $img);
            } catch (Exception $exception) {
                $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Возникла ошибка!', 'Товар был обновлен, но возникла ошибка при загрузке иконки: ' . $exception->getMessage())
                    ->withBack(admin_url('shop', 'product', 'edit', ['product' => $product->id]))
                    ->withBack(admin_url('shop', 'product'), 'Вернуться к списку товаров')
                    ->render();
            }
        }

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Товар обновлен.')
            ->withBack(admin_url('shop', 'product', 'edit', ['product' => $product->id]))
            ->withBack(admin_url('shop', 'product'), 'Вернуться к списку товаров')
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Shop\Exceptions\ProductNotFoundException
     */
    public function delete(Request $request)
    {
        $request->checkCsrf();
        $product = $this->getProduct($request);
        $this->getProductModel()->delete($product);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Товар удален.')
            ->withBack(admin_url('shop', 'product'))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Shop\Exceptions\ProductNotFoundException
     */
    public function toggleEnabled(Request $request)
    {
        $request->checkCsrf();
        $product = $this->getProduct($request, false);
        $product->enabled = !$product->enabled;
        $this->getProductModel()->update($product);

        $this->printJsonResponse(true, 'Успех!', 'Товар теперь ' . ($product->enabled ? 'видим' : 'скрыт'));
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     * @throws \App\Shop\Exceptions\ProductNotFoundException
     */
    public function give(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('amount', Validator::numericVal()->min(1)->max(999))
        );

        $product = $this->getProduct($request, false);
        $user = $this->getUser($request);
        $amount = (int) $request->post('amount');

        /* @var WarehouseModel $warehouseModel */
        $warehouseModel = $this->app->make(WarehouseModel::class);
        $warehouseModel->insert(Warehouse::createEntity($user, $product, $amount, 0));

        $this->printJsonResponse(true, 'Успех!', 'Товар ' . $product->name . ' (' . $amount . ' шт.) ' . ' выдан игроку ' . $user->name);
    }

    /**
     * @param Request $request
     */
    private function validate(Request $request): void
    {
        $request->validate(
            Validator::key('name', Validator::stringType()->length(1, 255))
                ->key('block_id', Validator::stringType()->length(1, 255))
                ->key('amount', Validator::numericVal()->min(1))
                ->key('price', Validator::numericVal()->min(0))
                ->key('enabled', Validator::boolVal(), false)
                ->key('enchants', Validator::arrayType())
        );
    }

    /**
     * @param Product $product
     * @param array $enchants
     */
    private function addEnchantsToProduct(Product $product, array $enchants): void
    {
        $enchants1 = $this->module->getConfig()->searchEnchants(array_keys($enchants), $product->server_id);
        foreach ($enchants1 as $id => $enchant)
        {
            if (isset($enchants[$id]) && ($level = (int) $enchants[$id]) > 0) {
                $enchant->setLevel($level);
                $product->addEnchant($enchant);
            }
        }
    }
}
