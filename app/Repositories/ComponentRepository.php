<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ComponentRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function countForAdmin(array $filters = []): int
    {
        [$whereSql, $params] = $this->buildFilterClause($filters);

        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM components ' . $whereSql);
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }

    public function paginatedForAdmin(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        [$whereSql, $params] = $this->buildFilterClause($filters);

        $statement = $this->pdo->prepare(
            'SELECT id, code, name, slug, category, unit, current_stock, reorder_level,
                    supplier_name, purchase_url, is_active
             FROM components ' . $whereSql . '
             ORDER BY id DESC
             LIMIT :limit OFFSET :offset'
        );

        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function allActive(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, code, name, unit, current_stock, reorder_level
             FROM components
             WHERE is_active = 1
             ORDER BY name ASC'
        );

        return $statement->fetchAll();
    }

    public function countReorderNeeded(): int
    {
        $statement = $this->pdo->query(
            'SELECT COUNT(*)
             FROM components
             WHERE is_active = 1
               AND current_stock <= reorder_level'
        );

        return (int) $statement->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, code, name, slug, category, description, unit, current_stock, reorder_level,
                    pack_quantity, last_price, supplier_name, supplier_sku, purchase_url,
                    location, lead_time_days, is_active, notes
             FROM components
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
            'INSERT INTO components (
                code, name, slug, category, description, unit, current_stock, reorder_level,
                pack_quantity, last_price, supplier_name, supplier_sku, purchase_url,
                location, lead_time_days, is_active, notes
             ) VALUES (
                :code, :name, :slug, :category, :description, :unit, :current_stock, :reorder_level,
                :pack_quantity, :last_price, :supplier_name, :supplier_sku, :purchase_url,
                :location, :lead_time_days, :is_active, :notes
             )'
        );

        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE components
             SET code = :code,
                 name = :name,
                 slug = :slug,
                 category = :category,
                 description = :description,
                 unit = :unit,
                 current_stock = :current_stock,
                 reorder_level = :reorder_level,
                 pack_quantity = :pack_quantity,
                 last_price = :last_price,
                 supplier_name = :supplier_name,
                 supplier_sku = :supplier_sku,
                 purchase_url = :purchase_url,
                 location = :location,
                 lead_time_days = :lead_time_days,
                 is_active = :is_active,
                 notes = :notes
             WHERE id = :id'
        );

        $statement->execute($data + ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM components WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private function buildFilterClause(array $filters): array
    {
        $where = ['1=1'];
        $params = [];

        if (($filters['q'] ?? '') !== '') {
            $where[] = '(name LIKE :q OR code LIKE :q OR category LIKE :q OR supplier_name LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        if (($filters['stock'] ?? '') === 'reorder') {
            $where[] = 'current_stock <= reorder_level';
        }

        if (($filters['status'] ?? '') !== '') {
            $where[] = 'is_active = :is_active';
            $params['is_active'] = $filters['status'] === 'active' ? 1 : 0;
        }

        return ['WHERE ' . implode(' AND ', $where), $params];
    }
}
