<?php


namespace App\Cabinet\Controllers\Admin;


use App\Core\Http\AdminController;

class IndexController extends AdminController
{
    /**
     * @return void
     */
    public function index()
    {
        $groups = $this->module->getConfig()->getGroups();

        $this->createView($this->module->getName())
            ->addBreadcrumb($this->module->getName())
            ->render('cabinet/index', [
                'groups' => $groups
            ]);
    }
}
