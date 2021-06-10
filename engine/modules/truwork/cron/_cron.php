<?php

@error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);

@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('AUTOMODE', true);
define('LOGGED_IN', true);

define('ROOT_DIR', __DIR__ . '/../../../../');
define('ENGINE_DIR', ROOT_DIR . '/engine');

require_once(ENGINE_DIR . '/classes/plugins.class.php');
require_once(DLEPlugins::Check(ENGINE_DIR . '/inc/include/functions.inc.php'));

date_default_timezone_set($config['date_adjust']);

$_REQUEST = array();
$_POST = array();
$_GET = array();
$_REQUEST['user_hash'] = 1;
$member_id = [];

include_once __DIR__ . '/../_init.php';
\App\Core\Database\DB::$displayErrors = false;
