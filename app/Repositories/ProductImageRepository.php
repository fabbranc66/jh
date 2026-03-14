<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProductImageRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary)
             VALUES (:product_id, :image_path, :alt_text, :sort_order, :is_primary)'
        );

        $statement->execute([
            'product_id' => $data['product_id'],
            'image_path' => $data['image_path'],
            'alt_text' => $data['alt_text'],
            'sort_order' => $data['sort_order'],
            'is_primary' => $data['is_primary'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function hasPrimaryImage(int $productId): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT id
             FROM product_images
             WHERE product_id = :product_id AND is_primary = 1
             LIMIT 1'
        );
        $statement->execute(['product_id' => $productId]);

        return (bool) $statement->fetchColumn();
    }

    public function byProductId(int $productId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, image_path, alt_text, sort_order, is_primary, created_at
             FROM product_images
             WHERE product_id = :product_id
             ORDER BY is_primary DESC, sort_order ASC, id ASC'
        );
        $statement->execute(['product_id' => $productId]);

        return $statement->fetchAll();
    }

    public function existsForProduct(int $productId, string $imagePath): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT id
             FROM product_images
             WHERE product_id = :product_id AND image_path = :image_path
             LIMIT 1'
        );
        $statement->execute([
            'product_id' => $productId,
            'image_path' => $imagePath,
        ]);

        return (bool) $statement->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, product_id, image_path, alt_text, sort_order, is_primary
             FROM product_images
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();

        return $result ?: null;
    }

    public function clearPrimaryForProduct(int $productId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE product_images
             SET is_primary = 0
             WHERE product_id = :product_id'
        );
        $statement->execute(['product_id' => $productId]);
    }

    public function setPrimary(int $imageId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE product_images
             SET is_primary = 1
             WHERE id = :id'
        );
        $statement->execute(['id' => $imageId]);
    }

    public function delete(int $imageId): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM product_images
             WHERE id = :id'
        );
        $statement->execute(['id' => $imageId]);
    }
}
