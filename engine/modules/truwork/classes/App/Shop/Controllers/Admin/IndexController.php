<?php


namespace App\Shop\Controllers\Admin;


use App\Core\Http\AdminController;

class IndexController extends AdminController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->createView($this->module->getName())
            ->addBreadcrumb($this->module->getName())
            ->render('shop/index', [
                //
            ]);
    }
}
