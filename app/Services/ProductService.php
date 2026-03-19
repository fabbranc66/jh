<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use RuntimeException;

final class ProductService
{
    public function __construct(
        private ProductRepository $products,
        private ProductImageRepository $images
    ) {
    }

    public function listAllForAdmin(array $filters = []): array
    {
        return $this->hydrateAdminProducts($this->products->paginatedForAdmin($filters, 500, 0));
    }

    public function paginateForAdmin(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $total = $this->products->countForAdmin($filters);
        $offset = ($page - 1) * $perPage;

        return [
            'items' => $this->hydrateAdminProducts($this->products->paginatedForAdmin($filters, $perPage, $offset)),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    private function hydrateAdminProducts(array $products): array
    {
        return array_map(function (array $product): array {
            $path = $product['primary_image_path'] ?? null;
            $product['primary_image_url'] = $path
                ? rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/' . ltrim($path, '/')
                : null;
            $product['images'] = array_map(function (array $image): array {
                $image['public_url'] = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/' . ltrim($image['image_path'], '/');
                return $image;
            }, $this->images->byProductId((int) $product['id']));

            return $product;
        }, $products);
    }

    public function latest(int $limit = 12): array
    {
        return $this->hydratePublicProducts($this->products->latestActive($limit));
    }

    public function search(string $query, int $limit = 24): array
    {
        $query = trim($query);

        if ($query === '') {
            return $this->latest($limit);
        }

        return $this->hydratePublicProducts($this->products->searchActive($query, $limit));
    }

    public function byCategory(int $categoryId, int $limit = 24): array
    {
        return $this->hydratePublicProducts($this->products->latestActiveByCategory($categoryId, $limit));
    }

    public function bySlugs(array $slugs): array
    {
        $items = [];

        foreach ($slugs as $slug) {
            $product = $this->findBySlug((string) $slug);
            if ($product !== null) {
                $items[] = $product;
            }
        }

        return $items;
    }

    public function findBySlug(string $slug): ?array
    {
        $product = $this->products->findActiveBySlug($slug);

        if ($product === null) {
            return null;
        }

        $product['request_message'] = rawurlencode(sprintf(
            "Ciao, sono interessato a questo prodotto:\n%s\nCodice: %s\nLink: %s/prodotto/%s",
            $product['name'],
            $product['sku'] ?: 'N/D',
            rtrim($_ENV['APP_URL'] ?? 'http://localhost/jh/public', '/'),
            $product['slug']
        ));
        $product['telegram_share_url'] = sprintf(
            'https://t.me/share/url?url=%s&text=%s',
            rawurlencode(rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/prodotto/' . $product['slug']),
            rawurlencode($product['name'])
        );

        return $product;
    }

    public function relatedByCategory(int $categoryId, int $excludeId, int $limit = 3): array
    {
        $items = array_filter(
            $this->byCategory($categoryId, $limit + 1),
            static fn (array $product): bool => (int) $product['id'] !== $excludeId
        );

        return array_slice(array_values($items), 0, $limit);
    }

    public function findById(int $id): ?array
    {
        return $this->products->findById($id);
    }

    public function create(array $input): int
    {
        return $this->products->create($this->normalizeProductInput($input));
    }

    public function update(int $id, array $input): void
    {
        if ($this->products->findById($id) === null) {
            throw new RuntimeException('Prodotto non trovato.');
        }

        $this->products->update($id, $this->normalizeProductInput($input));
    }

    public function delete(int $id): void
    {
        if ($this->products->findById($id) === null) {
            throw new RuntimeException('Prodotto non trovato.');
        }

        $this->products->delete($id);
    }

    private function normalizeProductInput(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        $status = (string) ($input['status'] ?? 'draft');

        if ((int) ($input['category_id'] ?? 0) <= 0 || $name === '') {
            throw new RuntimeException('Categoria e nome sono obbligatori.');
        }

        if (!in_array($status, ['draft', 'published'], true)) {
            throw new RuntimeException('Stato prodotto non valido.');
        }

        return [
            'category_id' => (int) ($input['category_id'] ?? 0),
            'name' => $name,
            'slug' => $this->slugify($name),
            'sku' => trim((string) ($input['sku'] ?? '')),
            'product_type' => $this->normalizeProductType((string) ($input['product_type'] ?? 'finished')),
            'short_description' => trim((string) ($input['short_description'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
            'materials' => trim((string) ($input['materials'] ?? '')),
            'technique' => trim((string) ($input['technique'] ?? '')),
            'production_time_hours' => $this->nullableDecimal($input['production_time_hours'] ?? null, 2),
            'internal_cost' => $this->nullableDecimal($input['internal_cost'] ?? null, 2),
            'minimum_stock' => $this->nullableDecimal($input['minimum_stock'] ?? null, 3),
            'internal_notes' => trim((string) ($input['internal_notes'] ?? '')),
            'price_label' => trim((string) ($input['price_label'] ?? 'Prezzo su richiesta')),
            'is_customizable' => isset($input['is_customizable']) ? 1 : 0,
            'is_featured' => isset($input['is_featured']) ? 1 : 0,
            'whatsapp_enabled' => isset($input['whatsapp_enabled']) ? 1 : 0,
            'telegram_enabled' => isset($input['telegram_enabled']) ? 1 : 0,
            'share_enabled' => isset($input['share_enabled']) ? 1 : 0,
            'status' => $status,
        ];
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'prodotto';
    }

    private function normalizeProductType(string $value): string
    {
        return in_array($value, ['finished', 'service', 'kit'], true) ? $value : 'finished';
    }

    private function nullableDecimal(mixed $value, int $decimals): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return number_format((float) $value, $decimals, '.', '');
    }

    private function hydratePublicProducts(array $products): array
    {
        return array_map(function (array $product): array {
            $path = $product['primary_image_path'] ?? null;
            $product['primary_image_url'] = $path
                ? rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/' . ltrim($path, '/')
                : null;

            return $product;
        }, $products);
    }
}
