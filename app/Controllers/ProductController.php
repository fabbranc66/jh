<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ProductService;

final class ProductController
{
    public function __construct(
        private View $view,
        private ProductService $products
    ) {
    }

    public function show(string $slug): void
    {
        $product = $this->products->findBySlug($slug);

        if ($product === null) {
            http_response_code(404);

            $this->view->render('pages/not-found.twig', [
                'pageTitle' => 'Prodotto non trovato',
                'message' => 'Il prodotto richiesto non e disponibile.',
            ]);
            return;
        }

        $this->view->render('pages/product.twig', [
            'pageTitle' => $product['name'],
            'product' => $product,
        ]);
    }
}
