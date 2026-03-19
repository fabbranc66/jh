<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\View;
use App\Services\AdminAuthService;
use App\Services\BrandingService;
use App\Services\CsrfService;
use App\Services\MenuService;
use App\Repositories\MenuItemRepository;
use App\Repositories\SettingRepository;
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

if (filter_var($_ENV['APP_DEBUG_BOOT'] ?? false, FILTER_VALIDATE_BOOL)) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo "APP_ENV=" . (string) ($_ENV['APP_ENV'] ?? '(missing)') . PHP_EOL;
    echo "APP_URL=" . (string) ($_ENV['APP_URL'] ?? '(missing)') . PHP_EOL;
    echo "DB_HOST=" . (string) ($_ENV['DB_HOST'] ?? '(missing)') . PHP_EOL;
    echo "DB_HOSTS=" . (string) ($_ENV['DB_HOSTS'] ?? '(missing)') . PHP_EOL;
    echo "DB_DATABASE=" . (string) ($_ENV['DB_DATABASE'] ?? '(missing)') . PHP_EOL;
    echo "DB_USERNAME=" . (string) ($_ENV['DB_USERNAME'] ?? '(missing)') . PHP_EOL;
    exit;
}

$appConfig = require BASE_PATH . '/config/app.php';
$dbConfig = require BASE_PATH . '/config/database.php';

if (PHP_SAPI !== 'cli') {
    $forwardedProto = trim(explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? '');
    $isHttps = $forwardedProto !== ''
        ? $forwardedProto === 'https'
        : (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $isHttps ? 'https' : 'http';
    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = preg_replace('#/index\.php$#', '', $scriptName) ?? '';
    $basePath = rtrim($basePath, '/');

    if ($host !== '') {
        $runtimeUrl = $scheme . '://' . $host . ($basePath !== '' ? $basePath : '');
        $appConfig['url'] = rtrim($runtimeUrl, '/');
        $_ENV['APP_URL'] = $appConfig['url'];
    }
}

date_default_timezone_set($appConfig['timezone']);

ini_set('display_errors', $appConfig['debug'] ? '1' : '0');
error_reporting($appConfig['debug'] ? E_ALL : 0);

$flight = new Engine();
$view = new View(BASE_PATH . '/app/Views', BASE_PATH . '/storage/cache', $appConfig);
$database = new Database($dbConfig);
$branding = new BrandingService(new SettingRepository($database->pdo()));
$menu = new MenuService(new MenuItemRepository($database->pdo()));
$adminAuth = new AdminAuthService();
$csrf = new CsrfService();

$view->addGlobal('branding', $branding->globals());
$view->addGlobal('navigation', [
    'main' => $menu->mainNavigation(),
]);
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
