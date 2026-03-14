<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allActive(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, slug, description, image, sort_order
             FROM categories
             WHERE is_active = 1
             ORDER BY sort_order ASC, name ASC'
        );

        return $statement->fetchAll();
    }

    public function findActiveBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, slug, description, image, sort_order
             FROM categories
             WHERE slug = :slug AND is_active = 1
             LIMIT 1'
        );

        $statement->execute(['slug' => $slug]);
        $result = $statement->fetch();

        return $result ?: null;
    }
}
