<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProductBomRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function byProductId(int $productId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT pbi.id, pbi.product_id, pbi.component_id, pbi.quantity, pbi.unit, pbi.waste_percent, pbi.notes,
                    c.code AS component_code, c.name AS component_name, c.current_stock, c.reorder_level,
                    c.purchase_url, c.supplier_name
             FROM product_bom_items pbi
             INNER JOIN components c ON c.id = pbi.component_id
             WHERE pbi.product_id = :product_id
             ORDER BY pbi.sort_order ASC, pbi.id ASC'
        );
        $statement->execute(['product_id' => $productId]);

        return $statement->fetchAll();
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO product_bom_items (
                product_id, component_id, quantity, unit, waste_percent, notes, sort_order
             ) VALUES (
                :product_id, :component_id, :quantity, :unit, :waste_percent, :notes, :sort_order
             )'
        );
        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM product_bom_items WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, product_id, component_id, quantity, unit, waste_percent, notes
             FROM product_bom_items
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();

        return $result ?: null;
    }
}
