<?php

if( !defined('DATALIFEENGINE') ) {
    header( "HTTP/1.1 403 Forbidden" );
    header ( 'Location: ../../' );
    die( 'Hacking attempt!' );
}

include '_init.php';
\App\Core\Http\Request::$PAGE_KEY = 'p';
\App\Core\View\View::$CONTENT_NAME = 'content2';

$control = isset($control) ? $control : 'index';
$action = isset($action) ? $action : 'index';

$router = new \App\Core\Http\Router($app, $module, $control, $action);
$router->runAsIncluded();
