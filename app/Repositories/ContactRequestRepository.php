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

    public function all(): array
    {
        $statement = $this->pdo->query(
            'SELECT cr.id, cr.name, cr.email, cr.phone, cr.message, cr.source, cr.status, cr.created_at,
                    p.name AS product_name
             FROM contact_requests cr
             LEFT JOIN products p ON p.id = cr.product_id
             ORDER BY cr.id DESC'
        );

        return $statement->fetchAll();
    }
}
