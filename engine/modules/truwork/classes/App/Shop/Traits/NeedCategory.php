<?php


namespace App\Shop\Traits;


use App\Core\Http\Request;
use App\Shop\Entity\Category;
use App\Shop\Exceptions\CategoryNotFoundException;
use App\Shop\Models\CategoryModel;
use Respect\Validation\Validator;

trait NeedCategory
{
    /**
     * @var CategoryModel
     */
    private $categoryModel;

    /**
     * @return CategoryModel
     */
    protected function getCategoryModel(): CategoryModel
    {
        return is_null($this->categoryModel)
            ? $this->categoryModel = $this->app->make(CategoryModel::class)
            : $this->categoryModel;
    }

    /**
     * @param Request $request
     * @return Category
     * @throws \App\Shop\Exceptions\CategoryNotFoundException
     */
    protected function getCategory(Request $request, bool $enabled = true): Category
    {
        $request->validateAny(Validator::key('category', Validator::numericVal()));
        $id = (int) $request->any('category');
        /* @var Category $category */
        $category = $this->getCategoryModel()->find($id);
        if (is_null($category) || ($enabled && !$category->enabled)) {
            throw new CategoryNotFoundException($id);
        }

        return $category;
    }
}
