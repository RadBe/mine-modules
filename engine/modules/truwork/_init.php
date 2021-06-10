<?php

if( !defined('DATALIFEENGINE') ) {
    header( "HTTP/1.1 403 Forbidden" );
    header ( 'Location: ../../' );
    die( 'Hacking attempt!' );
}

define('TW_PREFIX', 'tw');
define('TW_DIR', dirname(__FILE__));

include_once '_autoload.php';
include_once '_functions.php';
if (is_file($stringifyLib = TW_DIR . '/classes/Respect/Stringifier/stringify.php')) {
    include_once $stringifyLib;
}

if ($db->db_id === false) {
    $db->connect(DBUSER, DBPASS, DBNAME, DBHOST);
}

$app = \App\Core\Application::getInstance();

if (isset($mcache) && $mcache && $mcache->connection > 0) {
    $app->bind(\App\Core\Cache\CacheSystem::class, function () use ($mcache) {
        $server = (new ReflectionClass($mcache))->getProperty('server');
        $server->setAccessible(true);
        $memcache = $server->getValue($mcache);
        return new \App\Core\Cache\DLEMemCache($memcache);
    });
} else {
    $app->bind(\App\Core\Cache\CacheSystem::class, \App\Core\Cache\FileCache::class);
}

$app->init(new \App\Core\Database\MySQLi($db), new \App\DLEConfig($config ?? []), $member_id ?? null);
$request = $app->getRequest();

if (isset($tpl)) {
    $app->bind('tpl', $tpl);
}

if (isset($metatags)) {
    $meta = new \App\Core\View\Meta($metatags);
    $app->bind('meta', $meta);
} else {
    $app->bind('meta', function () {});
}
