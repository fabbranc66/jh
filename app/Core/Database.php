<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $hosts = $this->resolveHosts($config);
        $errors = [];
        $lastException = null;

        foreach ($hosts as $host) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $host,
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            try {
                $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 5,
                ]);
                return;
            } catch (PDOException $exception) {
                $lastException = $exception;
                $errors[] = sprintf('%s => %s', $host, $exception->getMessage());
            }
        }

        $message = 'Connessione database non riuscita.';
        if ($errors !== []) {
            $message .= ' Tentativi: ' . implode(' | ', $errors);
        }

        throw new PDOException($message, (int) ($lastException?->getCode() ?? 0), $lastException);
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    private function resolveHosts(array $config): array
    {
        $hosts = [];

        foreach (($config['hosts'] ?? []) as $host) {
            if ($host !== '' && !in_array($host, $hosts, true)) {
                $hosts[] = $host;
            }
        }

        $primaryHost = (string) ($config['host'] ?? '');
        if ($primaryHost !== '' && !in_array($primaryHost, $hosts, true)) {
            array_unshift($hosts, $primaryHost);
        }

        return $hosts;
    }
}
