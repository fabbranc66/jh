<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class HomeSlideRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allActive(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, title, subtitle, image_url, link_url, button_label, sort_order
             FROM home_slides
             WHERE is_active = 1
             ORDER BY sort_order ASC, id ASC'
        );

        return $statement->fetchAll();
    }

    public function allForAdmin(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, title, subtitle, image_url, link_url, button_label, sort_order, is_active
             FROM home_slides
             ORDER BY sort_order ASC, id ASC'
        );

        return $statement->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, title, subtitle, image_url, link_url, button_label, sort_order, is_active
             FROM home_slides
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
            'INSERT INTO home_slides (title, subtitle, image_url, link_url, button_label, sort_order, is_active)
             VALUES (:title, :subtitle, :image_url, :link_url, :button_label, :sort_order, :is_active)'
        );
        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE home_slides
             SET title = :title,
                 subtitle = :subtitle,
                 image_url = :image_url,
                 link_url = :link_url,
                 button_label = :button_label,
                 sort_order = :sort_order,
                 is_active = :is_active
             WHERE id = :id'
        );
        $statement->execute($data + ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM home_slides WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
