<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\CategoryService;
use App\Services\PageService;
use App\Services\ProductService;

final class CatalogController
{
    public function __construct(
        private View $view,
        private CategoryService $categories,
        private ProductService $products,
        private PageService $pages
    ) {
    }

    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        $page = $this->pages->findBySlug('catalogo');

        $this->view->render('pages/catalog.twig', [
            'pageTitle' => 'Catalogo',
            'pageBandClass' => 'page-band--catalog',
            'page' => $page,
            'categories' => $this->categories->listActive(),
            'products' => $query !== '' ? $this->products->search($query, 24) : $this->products->latest(24),
            'searchQuery' => $query,
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
            'pageBandClass' => $this->bandClassForCategory($slug),
            'category' => $category,
            'products' => $this->products->byCategory((int) $category['id']),
        ]);
    }

    private function bandClassForCategory(string $slug): string
    {
        return match ($slug) {
            'gioielli-wire', 'gioielli-resina', 'gioielli-ibridi' => 'page-band--jewelry',
            'oggettistica-resina', 'stampa-3d', 'segnaletica-personalizzata' => 'page-band--decor',
            'eventi' => 'page-band--events',
            'smart-objects', 'scanner-3d', 'reverse-engineering', 'accessori-vape' => 'page-band--smart',
            'pet' => 'page-band--pet',
            default => 'page-band--catalog',
        };
    }
}
