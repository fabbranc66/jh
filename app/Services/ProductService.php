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
        }, $this->products->allForAdmin($filters));
    }

    public function latest(int $limit = 12): array
    {
        return $this->products->latestActive($limit);
    }

    public function byCategory(int $categoryId, int $limit = 24): array
    {
        return $this->products->latestActiveByCategory($categoryId, $limit);
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

        return $product;
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

        if ((int) ($input['category_id'] ?? 0) <= 0 || $name === '') {
            throw new RuntimeException('Categoria e nome sono obbligatori.');
        }

        return [
            'category_id' => (int) ($input['category_id'] ?? 0),
            'name' => $name,
            'slug' => $this->slugify($name),
            'sku' => trim((string) ($input['sku'] ?? '')),
            'short_description' => trim((string) ($input['short_description'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
            'materials' => trim((string) ($input['materials'] ?? '')),
            'technique' => trim((string) ($input['technique'] ?? '')),
            'price_label' => trim((string) ($input['price_label'] ?? 'Prezzo su richiesta')),
            'is_customizable' => isset($input['is_customizable']) ? 1 : 0,
            'is_featured' => isset($input['is_featured']) ? 1 : 0,
            'whatsapp_enabled' => isset($input['whatsapp_enabled']) ? 1 : 0,
            'telegram_enabled' => isset($input['telegram_enabled']) ? 1 : 0,
            'share_enabled' => isset($input['share_enabled']) ? 1 : 0,
            'status' => (string) ($input['status'] ?? 'draft'),
        ];
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'prodotto';
    }
}
