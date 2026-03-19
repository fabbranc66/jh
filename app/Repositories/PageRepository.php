<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PageRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allForAdmin(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, title, slug, content, meta_title, meta_description, image_path, is_active
             FROM pages
             ORDER BY title ASC'
        );

        return $statement->fetchAll();
    }

    public function findActiveBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, title, slug, content, meta_title, meta_description, image_path
             FROM pages
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
            'SELECT id, title, slug, content, meta_title, meta_description, image_path, is_active
             FROM pages
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();

        return $result ?: null;
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE pages
             SET title = :title,
                 slug = :slug,
                 content = :content,
                 meta_title = :meta_title,
                 meta_description = :meta_description,
                 image_path = :image_path,
                 is_active = :is_active
             WHERE id = :id'
        );

        $statement->execute($data + ['id' => $id]);
    }

    public function upsertPage(array $data): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO pages (title, slug, content, meta_title, meta_description, image_path, is_active)
             VALUES (:title, :slug, :content, :meta_title, :meta_description, :image_path, :is_active)
             ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                content = VALUES(content),
                meta_title = VALUES(meta_title),
                meta_description = VALUES(meta_description),
                image_path = VALUES(image_path),
                is_active = VALUES(is_active),
                updated_at = CURRENT_TIMESTAMP'
        );
        $statement->execute($data);
    }
}
