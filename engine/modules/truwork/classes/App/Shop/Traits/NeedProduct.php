<?php


namespace App\Shop\Traits;


use App\Core\Http\Request;
use App\Shop\Entity\Product;
use App\Shop\Exceptions\ProductNotFoundException;
use App\Shop\Models\ProductModel;
use Respect\Validation\Validator;

trait NeedProduct
{
    /**
     * @var ProductModel
     */
    private $productModel;

    /**
     * @return ProductModel
     */
    protected function getProductModel(): ProductModel
    {
        return is_null($this->productModel)
            ? $this->productModel = $this->app->make(ProductModel::class)
            : $this->productModel;
    }

    /**
     * @param Request $request
     * @param bool $enabled
     * @return Product
     * @throws \App\Shop\Exceptions\ProductNotFoundException
     */
    protected function getProduct(Request $request, bool $enabled = true): Product
    {
        $request->validateAny(Validator::key('product', Validator::numericVal()));
        $id = (int) $request->any('product');
        /* @var Product $product */
        $product = $this->getProductModel()->find($id);
        if (is_null($product) || ($enabled && !$product->enabled)) {
            throw new ProductNotFoundException($id);
        }

        return $product;
    }
}
