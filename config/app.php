<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'JH',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost/jh/public', '/'),
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Europe/Rome',
    'asset_version' => $_ENV['APP_ASSET_VERSION'] ?? '20260314-1',
];
