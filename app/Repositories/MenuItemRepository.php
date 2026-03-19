<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class MenuItemRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allActive(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, parent_id, label, url, image_path, sort_order, is_active
             FROM menu_items
             WHERE is_active = 1
             ORDER BY COALESCE(parent_id, 0) ASC, sort_order ASC, id ASC'
        );

        return $statement->fetchAll();
    }

    public function allForAdmin(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, parent_id, label, url, image_path, sort_order, is_active
             FROM menu_items
             ORDER BY COALESCE(parent_id, 0) ASC, sort_order ASC, id ASC'
        );

        return $statement->fetchAll();
    }

    public function topLevelForAdmin(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, label, sort_order
             FROM menu_items
             WHERE parent_id IS NULL
             ORDER BY sort_order ASC, id ASC'
        );

        return $statement->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, parent_id, label, url, image_path, sort_order, is_active
             FROM menu_items
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
            'INSERT INTO menu_items (parent_id, label, url, image_path, sort_order, is_active)
             VALUES (:parent_id, :label, :url, :image_path, :sort_order, :is_active)'
        );
        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE menu_items
             SET parent_id = :parent_id,
                 label = :label,
                 url = :url,
                 image_path = :image_path,
                 sort_order = :sort_order,
                 is_active = :is_active
             WHERE id = :id'
        );
        $statement->execute($data + ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM menu_items WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function previousSibling(int $id, ?int $parentId, int $sortOrder): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, sort_order
             FROM menu_items
             WHERE ' . ($parentId === null ? 'parent_id IS NULL' : 'parent_id = :parent_id') . '
               AND (
                    sort_order < :current_sort_order
                    OR (sort_order = :same_sort_order AND id < :current_id)
               )
             ORDER BY sort_order DESC, id DESC
             LIMIT 1'
        );

        $params = [
            'current_sort_order' => $sortOrder,
            'same_sort_order' => $sortOrder,
            'current_id' => $id,
        ];
        if ($parentId !== null) {
            $params['parent_id'] = $parentId;
        }

        $statement->execute($params);
        $result = $statement->fetch();

        return $result ?: null;
    }

    public function nextSibling(int $id, ?int $parentId, int $sortOrder): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, sort_order
             FROM menu_items
             WHERE ' . ($parentId === null ? 'parent_id IS NULL' : 'parent_id = :parent_id') . '
               AND (
                    sort_order > :current_sort_order
                    OR (sort_order = :same_sort_order AND id > :current_id)
               )
             ORDER BY sort_order ASC, id ASC
             LIMIT 1'
        );

        $params = [
            'current_sort_order' => $sortOrder,
            'same_sort_order' => $sortOrder,
            'current_id' => $id,
        ];
        if ($parentId !== null) {
            $params['parent_id'] = $parentId;
        }

        $statement->execute($params);
        $result = $statement->fetch();

        return $result ?: null;
    }

    public function updateSortOrder(int $id, int $sortOrder): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE menu_items
             SET sort_order = :sort_order
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'sort_order' => $sortOrder,
        ]);
    }
}
