<?php


namespace App\Shop\Controllers;


use App\Core\Exceptions\NotEnoughMoneyException;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Models\UserModel;
use App\Shop\Entity\Product;
use App\Shop\Entity\Warehouse;
use App\Shop\Events\BuyProductEvent;
use App\Shop\Models\WarehouseModel;
use App\Shop\Traits\NeedCategory;
use App\Shop\Traits\NeedProduct;
use Respect\Validation\Validator;

class CatalogController extends Controller
{
    use NeedProduct, NeedServer, NeedCategory;

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\Exception
     * @throws \App\Core\Exceptions\ServerNotFoundException
     * @throws \App\Shop\Exceptions\CategoryNotFoundException
     */
    public function search(Request $request)
    {
        $request->validate(
            Validator::key('server', Validator::numericVal(), false)
        );
        $name = $request->post('name');
        $serverId = $request->post('server');
        if (!empty($serverId)) {
            $server = $this->getServer($request);
        }
        $categoryId = $request->post('category');
        if (!empty($categoryId)) {
            $category = $this->getCategory($request);
        }

        $products = $this->getProductModel()->search(
            isset($server) ? $server->id : null,
            isset($category) ? $category->id : null,
            $name
        );

        $this->printJsonData([
            'products' => array_map(function (Product $product) {
                return $product->toArray();
            }, $products->getResult()),
            'pagination' => $products->paginationData()
        ]);
        die;
    }

    /**
     * @param Request $request
     * @throws NotEnoughMoneyException
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     * @throws \App\Shop\Exceptions\ProductNotFoundException
     */
    public function buy(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('amount', Validator::numericVal()->min(1))
        );

        $server = $this->getServer($request);
        $product = $this->getProduct($request);
        $amount = (int) $request->post('amount');
        $price = $product->price * $amount;

        if (!$request->user()->hasMoney($price)) {
            throw new NotEnoughMoneyException($price);
        }

        $request->user()->withdrawMoney($price);
        $this->app->make(UserModel::class)->updateBalance($request->user());
        $this->app->make(WarehouseModel::class)->insert(
            Warehouse::createEntity($request->user(), $product, $amount, $price)
        );
        $product->increment('buys');
        $this->productModel->update($product);

        dispatch(new BuyProductEvent($request->user(), $server, $product));

        $this->printJsonResponse(true, 'Успех!', sprintf('Товар "%s" (%s шт.) добавлен на склад', $product->name, $amount), [
            'balance' => $request->user()->getMoney()
        ]);
        die;
    }
}
