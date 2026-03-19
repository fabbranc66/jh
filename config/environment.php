<?php

declare(strict_types=1);

$httpHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));

if ($httpHost !== '' && str_contains($httpHost, 'kr-solutions.it')) {
    return 'production';
}

$environment = trim((string) ($_ENV['APP_ENV'] ?? ''));

if ($environment !== '') {
    return match ($environment) {
        'production' => 'production',
        'lan' => 'lan',
        default => 'local',
    };
}

if ($httpHost !== '') {
    if ($httpHost !== 'localhost' && $httpHost !== '127.0.0.1') {
        return 'lan';
    }
}

return 'local';
