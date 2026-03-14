<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\CategoryService;
use App\Services\ProductService;

final class CatalogController
{
    public function __construct(
        private View $view,
        private CategoryService $categories,
        private ProductService $products
    ) {
    }

    public function index(): void
    {
        $this->view->render('pages/catalog.twig', [
            'pageTitle' => 'Catalogo',
            'categories' => $this->categories->listActive(),
            'products' => $this->products->latest(24),
        ]);
    }

    public function category(string $slug): void
    {
        $category = $this->categories->findBySlug($slug);

        if ($category === null) {
            http_response_code(404);

            $this->view->render('pages/not-found.twig', [
                'pageTitle' => 'Categoria non trovata',
                'message' => 'La categoria richiesta non e disponibile.',
            ]);
            return;
        }

        $this->view->render('pages/category.twig', [
            'pageTitle' => $category['name'],
            'category' => $category,
            'products' => $this->products->byCategory((int) $category['id']),
        ]);
    }
}
