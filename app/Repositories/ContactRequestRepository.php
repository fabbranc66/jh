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

    public function count(array $filters = []): int
    {
        [$whereSql, $params] = $this->buildFilterClause($filters);

        $statement = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM contact_requests cr
             LEFT JOIN products p ON p.id = cr.product_id
             ' . $whereSql
        );
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }

    public function paginated(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $sql = 'SELECT cr.id, cr.name, cr.email, cr.phone, cr.message, cr.source, cr.status, cr.created_at,
                       p.name AS product_name
                FROM contact_requests cr
                LEFT JOIN products p ON p.id = cr.product_id ';
        [$whereSql, $params] = $this->buildFilterClause($filters);
        $sql .= $whereSql . ' ORDER BY cr.id DESC LIMIT :limit OFFSET :offset';

        $statement = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

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

    private function buildFilterClause(array $filters): array
    {
        $where = ['1=1'];
        $params = [];

        if (($filters['q'] ?? '') !== '') {
            $where[] = '(cr.name LIKE :q OR cr.email LIKE :q OR cr.message LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        if (($filters['status'] ?? '') !== '') {
            $where[] = 'cr.status = :status';
            $params['status'] = $filters['status'];
        }

        return ['WHERE ' . implode(' AND ', $where), $params];
    }
}
