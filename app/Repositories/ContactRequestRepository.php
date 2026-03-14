<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ContactRequestRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO contact_requests (name, email, phone, message, product_id, source, status)
             VALUES (:name, :email, :phone, :message, :product_id, :source, :status)'
        );

        $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'],
            'product_id' => $data['product_id'],
            'source' => $data['source'],
            'status' => $data['status'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT cr.id, cr.name, cr.email, cr.phone, cr.message, cr.source, cr.status, cr.created_at,
                       p.name AS product_name
                FROM contact_requests cr
                LEFT JOIN products p ON p.id = cr.product_id
                WHERE 1=1';
        $params = [];

        if (($filters['q'] ?? '') !== '') {
            $sql .= ' AND (cr.name LIKE :q OR cr.email LIKE :q OR cr.message LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        if (($filters['status'] ?? '') !== '') {
            $sql .= ' AND cr.status = :status';
            $params['status'] = $filters['status'];
        }

        $sql .= ' ORDER BY cr.id DESC';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE contact_requests
             SET status = :status
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }
}
