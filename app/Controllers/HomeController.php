<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\CategoryService;
use App\Services\HomeSlideService;
use App\Services\ProductService;

final class HomeController
{
    public function __construct(
        private View $view,
        private CategoryService $categories,
        private ProductService $products,
        private HomeSlideService $slides
    ) {
    }

    public function index(): void
    {
        $categories = $this->categories->listActive();
        $featuredProducts = $this->products->latest(6);

        $this->view->render('pages/home.twig', [
            'pageTitle' => 'Laboratorio creativo handmade e digital craft',
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'slides' => $this->slides->listActive(),
            'stats' => [
                'categories' => count($categories),
                'products' => count($featuredProducts),
            ],
        ]);
    }
}
