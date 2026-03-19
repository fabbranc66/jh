<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class SettingRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findValue(string $key): ?string
    {
        $statement = $this->pdo->prepare(
            'SELECT setting_value
             FROM site_settings
             WHERE setting_key = :setting_key
             LIMIT 1'
        );
        $statement->execute(['setting_key' => $key]);
        $value = $statement->fetchColumn();

        return $value === false ? null : (string) $value;
    }

    public function upsert(string $key, string $value): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO site_settings (setting_key, setting_value)
             VALUES (:setting_key, :setting_value)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP'
        );
        $statement->execute([
            'setting_key' => $key,
            'setting_value' => $value,
        ]);
    }
}
