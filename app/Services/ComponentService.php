<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ComponentRepository;
use RuntimeException;

final class ComponentService
{
    public function __construct(private ComponentRepository $components)
    {
    }

    public function paginateForAdmin(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $total = $this->components->countForAdmin($filters);
        $offset = ($page - 1) * $perPage;

        return [
            'items' => $this->components->paginatedForAdmin($filters, $perPage, $offset),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function listActive(): array
    {
        return $this->components->allActive();
    }

    public function countReorderNeeded(): int
    {
        return $this->components->countReorderNeeded();
    }

    public function findById(int $id): ?array
    {
        return $this->components->findById($id);
    }

    public function create(array $input): int
    {
        return $this->components->create($this->normalizeInput($input));
    }

    public function update(int $id, array $input): void
    {
        if ($this->components->findById($id) === null) {
            throw new RuntimeException('Componente non trovato.');
        }

        $this->components->update($id, $this->normalizeInput($input));
    }

    public function delete(int $id): void
    {
        if ($this->components->findById($id) === null) {
            throw new RuntimeException('Componente non trovato.');
        }

        $this->components->delete($id);
    }

    private function normalizeInput(array $input): array
    {
        $code = strtoupper(trim((string) ($input['code'] ?? '')));
        $name = trim((string) ($input['name'] ?? ''));
        $url = trim((string) ($input['purchase_url'] ?? ''));

        if ($code === '' || $name === '') {
            throw new RuntimeException('Codice e nome componente sono obbligatori.');
        }

        if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Il link di acquisto non e valido.');
        }

        return [
            'code' => $code,
            'name' => $name,
            'slug' => $this->slugify($name),
            'category' => trim((string) ($input['category'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
            'unit' => trim((string) ($input['unit'] ?? 'pz')) ?: 'pz',
            'current_stock' => $this->decimalOrZero($input['current_stock'] ?? null),
            'reorder_level' => $this->decimalOrZero($input['reorder_level'] ?? null),
            'pack_quantity' => $this->nullableDecimal($input['pack_quantity'] ?? null),
            'last_price' => $this->nullableDecimal($input['last_price'] ?? null),
            'supplier_name' => trim((string) ($input['supplier_name'] ?? '')),
            'supplier_sku' => trim((string) ($input['supplier_sku'] ?? '')),
            'purchase_url' => $url,
            'location' => trim((string) ($input['location'] ?? '')),
            'lead_time_days' => $this->nullableInt($input['lead_time_days'] ?? null),
            'is_active' => isset($input['is_active']) ? 1 : 0,
            'notes' => trim((string) ($input['notes'] ?? '')),
        ];
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'componente';
    }

    private function decimalOrZero(mixed $value): string
    {
        return number_format((float) $value, 3, '.', '');
    }

    private function nullableDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return number_format((float) $value, 3, '.', '');
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return max(0, (int) $value);
    }
}
