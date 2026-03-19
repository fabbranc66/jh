<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ComponentRepository;
use App\Repositories\ProductBomRepository;
use App\Repositories\ProductRepository;
use RuntimeException;

final class ProductBomService
{
    public function __construct(
        private ProductBomRepository $bom,
        private ProductRepository $products,
        private ComponentRepository $components
    ) {
    }

    public function byProductId(int $productId): array
    {
        return $this->bom->byProductId($productId);
    }

    public function addItem(int $productId, array $input): int
    {
        if ($this->products->findById($productId) === null) {
            throw new RuntimeException('Prodotto non trovato.');
        }

        $componentId = (int) ($input['component_id'] ?? 0);
        if ($componentId <= 0 || $this->components->findById($componentId) === null) {
            throw new RuntimeException('Seleziona un componente valido.');
        }

        $quantity = (float) ($input['quantity'] ?? 0);
        if ($quantity <= 0) {
            throw new RuntimeException('La quantita deve essere maggiore di zero.');
        }

        return $this->bom->create([
            'product_id' => $productId,
            'component_id' => $componentId,
            'quantity' => number_format($quantity, 3, '.', ''),
            'unit' => trim((string) ($input['unit'] ?? 'pz')) ?: 'pz',
            'waste_percent' => number_format(max(0, (float) ($input['waste_percent'] ?? 0)), 2, '.', ''),
            'notes' => trim((string) ($input['notes'] ?? '')),
            'sort_order' => 0,
        ]);
    }

    public function removeItem(int $bomItemId): array
    {
        $item = $this->bom->findById($bomItemId);
        if ($item === null) {
            throw new RuntimeException('Voce distinta base non trovata.');
        }

        $this->bom->delete($bomItemId);

        return $item;
    }
}
