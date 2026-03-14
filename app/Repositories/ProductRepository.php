<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProductRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allForAdmin(): array
    {
        $statement = $this->pdo->query(
            'SELECT p.id, p.name, p.slug, p.sku, p.price_label, p.status, p.is_featured, p.is_customizable,
                    c.name AS category_name,
                    (
                        SELECT pi.image_path
                        FROM product_images pi
                        WHERE pi.product_id = p.id
                        ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.id ASC
                        LIMIT 1
                    ) AS primary_image_path
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             ORDER BY p.id DESC'
        );

        return $statement->fetchAll();
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

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.id, p.category_id, p.name, p.slug, p.sku, p.short_description, p.description,
                    p.materials, p.technique, p.price_label, p.is_customizable, p.is_featured,
                    p.whatsapp_enabled, p.telegram_enabled, p.share_enabled, p.status
             FROM products p
             WHERE p.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();

        return $result ?: null;
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE products
             SET category_id = :category_id,
                 name = :name,
                 slug = :slug,
                 sku = :sku,
                 short_description = :short_description,
                 description = :description,
                 materials = :materials,
                 technique = :technique,
                 price_label = :price_label,
                 is_customizable = :is_customizable,
                 is_featured = :is_featured,
                 whatsapp_enabled = :whatsapp_enabled,
                 telegram_enabled = :telegram_enabled,
                 share_enabled = :share_enabled,
                 status = :status
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'materials' => $data['materials'],
            'technique' => $data['technique'],
            'price_label' => $data['price_label'],
            'is_customizable' => $data['is_customizable'],
            'is_featured' => $data['is_featured'],
            'whatsapp_enabled' => $data['whatsapp_enabled'],
            'telegram_enabled' => $data['telegram_enabled'],
            'share_enabled' => $data['share_enabled'],
            'status' => $data['status'],
        ]);
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO products (
                category_id, name, slug, sku, short_description, description, materials, technique,
                price_label, is_customizable, is_featured, whatsapp_enabled, telegram_enabled, share_enabled, status
             ) VALUES (
                :category_id, :name, :slug, :sku, :short_description, :description, :materials, :technique,
                :price_label, :is_customizable, :is_featured, :whatsapp_enabled, :telegram_enabled, :share_enabled, :status
             )'
        );

        $statement->execute([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'materials' => $data['materials'],
            'technique' => $data['technique'],
            'price_label' => $data['price_label'],
            'is_customizable' => $data['is_customizable'],
            'is_featured' => $data['is_featured'],
            'whatsapp_enabled' => $data['whatsapp_enabled'],
            'telegram_enabled' => $data['telegram_enabled'],
            'share_enabled' => $data['share_enabled'],
            'status' => $data['status'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM products
             WHERE id = :id'
        );

        $statement->execute(['id' => $id]);
    }
}
