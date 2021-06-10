<?php


namespace App\Core\Http;


class AdminRouter extends Router
{
    /**
     * @inheritDoc
     */
    protected function getControllerClass(): string
    {
        $path = (!empty($this->namespace) ? $this->namespace . '/' : 'App/') .
            $this->getModuleName() . '/Controllers/Admin/' . $this->getControllerName() . 'Controller';

        return str_replace('/', '\\', $path);
    }
}
