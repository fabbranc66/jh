<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, slug, description, image, sort_order, is_active, created_at, updated_at
             FROM categories
             ORDER BY sort_order ASC, name ASC'
        );

        return $statement->fetchAll();
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

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, slug, description, image, sort_order, is_active
             FROM categories
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();

        return $result ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO categories (name, slug, description, image, sort_order, is_active)
             VALUES (:name, :slug, :description, :image, :sort_order, :is_active)'
        );

        $statement->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'image' => $data['image'],
            'sort_order' => $data['sort_order'],
            'is_active' => $data['is_active'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE categories
             SET name = :name,
                 slug = :slug,
                 description = :description,
                 image = :image,
                 sort_order = :sort_order,
                 is_active = :is_active
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'image' => $data['image'],
            'sort_order' => $data['sort_order'],
            'is_active' => $data['is_active'],
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM categories
             WHERE id = :id'
        );

        $statement->execute(['id' => $id]);
    }
}
