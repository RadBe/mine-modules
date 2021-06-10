<?php

if( !defined('DATALIFEENGINE') ) {
    header( "HTTP/1.1 403 Forbidden" );
    header ( 'Location: ../../' );
    die( 'Hacking attempt!' );
}

if (!function_exists('abort')) {
    function abort(string $message, int $status = 500) {
        header($_SERVER['SERVER_PROTOCOL'] . " $status $message", true, $status);
        die;
    }
}

if (!function_exists('tw_csrf')) {
    function tw_csrf(bool $getInput = false) {
        $csrf = \App\Core\Application::getInstance()->getRequest()->getCsrfToken();

        if ($getInput) {
            return '<input type="hidden" name="tw_csrf" value="' . $csrf . '">';
        }

        return $csrf;
    }
}

if (!function_exists('dispatch')) {
    function dispatch(object $event) {
        \App\Core\Events\EventManager::dispatch($event);
    }
}

if (!function_exists('dd')) {
    function dd(...$data) {
        print '<pre>';
        var_dump($data);
        die;
    }
}

if (!function_exists('ddd')) {
    function ddd(...$data) {
        print '<pre>';
        print_r($data);
        die;
    }
}

if (!function_exists('admin_url')) {
    function admin_url(string $module, string $controller = 'index', string $action = 'index', array $params = []) {
        return \App\Core\Support\URL::create($module, $controller, $action, $params);
    }
}

if (!function_exists('ajax_url')) {
    function ajax_url(string $module, string $controller, string $action, array $params = []) {
        return '/engine/ajax/controller.php' . \App\Core\Support\URL::create($module, $controller, $action, $params);
    }
}

if (!function_exists('admin_ajax_url')) {
    function admin_ajax_url(string $module, string $controller, string $action, array $params = []) {
        return '/engine/ajax/controller.php' . \App\Core\Support\URL::create($module, $controller, $action, $params)
            . '&admin_mode=1';
    }
}

if (!function_exists('base_url')) {
    function base_url(string $module, string $controller = 'index', string $action = 'index', array $params = []) {
        return '/' . \App\Core\Support\URL::createBase($module, $controller, $action, $params);
    }
}

if (!function_exists('optional')) {
    function optional(?object $object) {
        return new \App\Core\Support\Optional($object);
    }
}
