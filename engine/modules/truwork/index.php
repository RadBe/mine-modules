<?php

if( !defined('DATALIFEENGINE') ) {
    header( "HTTP/1.1 403 Forbidden" );
    header ( 'Location: ../../' );
    die( 'Hacking attempt!' );
}

include_once '_init.php';

$router = new \App\Core\Http\Router(
    $app,
    $request->get('module'),
    $request->get('control', 'index'),
    $request->get('action', 'index')
);
$router->run();

$metatags = $meta->getData();
