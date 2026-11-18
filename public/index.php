<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$app = require BASE_PATH . '/config/app.php';
define('APP_DEBUG', (bool) $app['debug']);
date_default_timezone_set($app['timezone']);

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

require BASE_PATH . '/core/helpers.php';
require BASE_PATH . '/core/Database.php';
require BASE_PATH . '/core/Router.php';

Router::dispatch($_GET['r'] ?? 'home/index');
