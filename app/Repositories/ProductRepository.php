<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProductRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function latestActive(int $limit = 12): array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.id, p.name, p.slug, p.sku, p.short_description, p.price_label,
                    p.is_customizable, p.is_featured, c.name AS category_name, c.slug AS category_slug
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.status = :status AND c.is_active = 1
             ORDER BY p.is_featured DESC, p.id DESC
             LIMIT :limit'
        );
        $statement->bindValue(':status', 'published');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function latestActiveByCategory(int $categoryId, int $limit = 24): array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.id, p.name, p.slug, p.sku, p.short_description, p.description, p.materials,
                    p.technique, p.price_label, p.is_customizable, p.is_featured,
                    c.name AS category_name, c.slug AS category_slug
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.category_id = :category_id
               AND p.status = :status
               AND c.is_active = 1
             ORDER BY p.is_featured DESC, p.id DESC
             LIMIT :limit'
        );
        $statement->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':status', 'published');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findActiveBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.id, p.name, p.slug, p.sku, p.short_description, p.description, p.materials,
                    p.technique, p.price_label, p.is_customizable, p.is_featured,
                    p.whatsapp_enabled, p.telegram_enabled, p.share_enabled,
                    c.name AS category_name, c.slug AS category_slug
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.slug = :slug
               AND p.status = :status
               AND c.is_active = 1
             LIMIT 1'
        );

        $statement->execute([
            'slug' => $slug,
            'status' => 'published',
        ]);

        $result = $statement->fetch();

        return $result ?: null;
    }
}
