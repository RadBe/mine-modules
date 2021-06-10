<?php


namespace App\Shop\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Models\ServersModel;
use App\Core\Support\AttachRelationEntity;
use App\Core\View\AdminAlert;
use App\Shop\Entity\Category;
use App\Shop\Traits\NeedCategory;
use Respect\Validation\Validator;

class CategoryController extends AdminController
{
    use NeedCategory, NeedServer;

    /**
     * @throws \App\Core\Exceptions\Exception
     */
    public function index()
    {
        $categories = $this->getCategoryModel()->getAll();
        AttachRelationEntity::make($categories, $this->app->make(ServersModel::class), 'server_id');

        $this->createView('Управление категориями')
            ->addBreadcrumb($this->module->getName(), admin_url('shop'))
            ->addBreadcrumb('Управление категориями')
            ->render('shop/categories/index', [
                'categories' => $categories,
                'servers' => $this->getServersModel()->getEnabled()
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function create(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('name', Validator::stringType()->length(1, 255))
                ->key('enabled', Validator::boolVal(), false)
        );

        $server = null;
        if (!empty($request->post('server'))) {
            $server = $this->getServer($request);
        }
        $category = Category::create([
            'name' => htmlspecialchars($request->post('name')),
            'enabled' => (bool) $request->post('enabled', false),
            'server_id' => optional($server)->id
        ]);
        $this->getCategoryModel()->insert($category);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Категория добавлена.')
            ->withBack(admin_url('shop', 'category'))
            ->withBack(admin_url('shop', 'category', 'edit', ['category' => $category->id]), 'Редактировать')
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Shop\Exceptions\CategoryNotFoundException
     */
    public function edit(Request $request)
    {
        $category = $this->getCategory($request, false);
        $this->createView('Редактирование категории #' . $category->getId())
            ->addBreadcrumb($this->module->getName(), admin_url('shop'))
            ->addBreadcrumb('Управление категориями', admin_url('shop', 'category'))
            ->addBreadcrumb('Редактирование категории #' . $category->getId())
            ->render('shop/categories/edit', [
                'category' => $category,
                'servers' => $this->getServersModel()->getEnabled()
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Core\Exceptions\ServerNotFoundException
     * @throws \App\Shop\Exceptions\CategoryNotFoundException
     */
    public function update(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('name', Validator::stringType()->length(1, 255))
                ->key('enabled', Validator::boolVal(), false)
        );
        $category = $this->getCategory($request, false);
        $server = null;
        if (!empty($request->post('server'))) {
            $server = $this->getServer($request);
        }

        $category->fill([
            'name' => htmlspecialchars($request->post('name')),
            'enabled' => (bool) $request->post('enabled', false),
            'server_id' => optional($server)->id
        ]);
        $this->getCategoryModel()->update($category);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Категория обновлена.')
            ->withBack(admin_url('shop', 'category', 'edit', ['category' => $category->id]))
            ->withBack(admin_url('shop', 'category'), 'Вернуться к списку категорий')
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Shop\Exceptions\CategoryNotFoundException
     */
    public function delete(Request $request)
    {
        $category = $this->getCategory($request, false);
        $this->getCategoryModel()->delete($category);

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Категория удалена. Товары из категори удалены.')
            ->withBack(admin_url('shop', 'category'))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     * @throws \App\Shop\Exceptions\CategoryNotFoundException
     */
    public function toggleEnabled(Request $request)
    {
        $request->checkCsrf();
        $category = $this->getCategory($request, false);
        $category->enabled = !$category->enabled;
        $this->getCategoryModel()->update($category);

        $this->printJsonResponse(true, 'Успех!', 'Категория теперь ' . ($category->enabled ? 'видима' : 'скрыта'));
    }
}
