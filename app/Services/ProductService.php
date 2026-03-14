<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductRepository;

final class ProductService
{
    public function __construct(private ProductRepository $products)
    {
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
}
