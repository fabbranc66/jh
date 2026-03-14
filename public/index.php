<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\View;
use Dotenv\Dotenv;
use flight\Engine;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

if (is_file(BASE_PATH . '/.env')) {
    Dotenv::createImmutable(BASE_PATH)->safeLoad();
}

$appConfig = require BASE_PATH . '/config/app.php';
$dbConfig = require BASE_PATH . '/config/database.php';

date_default_timezone_set($appConfig['timezone']);

ini_set('display_errors', $appConfig['debug'] ? '1' : '0');
error_reporting($appConfig['debug'] ? E_ALL : 0);

$flight = new Engine();
$view = new View(BASE_PATH . '/app/Views', BASE_PATH . '/storage/cache', $appConfig);
$database = new Database($dbConfig);

$flight->set('config', $appConfig);
$flight->set('db', $database->pdo());
$flight->set('view', $view);

require BASE_PATH . '/config/routes.php';

$flight->start();
