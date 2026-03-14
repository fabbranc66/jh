<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PageRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findActiveBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, title, slug, content, meta_title, meta_description
             FROM pages
             WHERE slug = :slug AND is_active = 1
             LIMIT 1'
        );
        $statement->execute(['slug' => $slug]);

        $result = $statement->fetch();

        return $result ?: null;
    }
}
