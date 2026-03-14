<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\View;
use App\Services\AdminAuthService;
use App\Services\CsrfService;
use Dotenv\Dotenv;
use flight\Engine;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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
$adminAuth = new AdminAuthService();
$csrf = new CsrfService();

$view->addGlobal('admin', [
    'authenticated' => $adminAuth->isAuthenticated(),
    'username' => $adminAuth->username(),
]);
$view->addGlobal('csrf', [
    'token' => $csrf->token(),
]);

$flight->set('config', $appConfig);
$flight->set('db', $database->pdo());
$flight->set('view', $view);
$flight->set('adminAuth', $adminAuth);
$flight->set('csrf', $csrf);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !$csrf->validate($_POST['_csrf'] ?? null)) {
    http_response_code(419);
    echo 'Sessione scaduta o richiesta non valida.';
    exit;
}

require BASE_PATH . '/config/routes.php';

$flight->start();
