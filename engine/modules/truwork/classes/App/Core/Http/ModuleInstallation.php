<?php


namespace App\Core\Http;


interface ModuleInstallation
{
    /**
     * @return void
     */
    public function index(): void;

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function install(Request $request): void;
}
