<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\CategoryService;
use App\Services\ProductService;

final class HomeController
{
    public function __construct(
        private View $view,
        private CategoryService $categories,
        private ProductService $products
    ) {
    }

    public function index(): void
    {
        $this->view->render('pages/home.twig', [
            'pageTitle' => 'Laboratorio creativo handmade e digital craft',
            'categories' => $this->categories->listActive(),
            'featuredProducts' => $this->products->latest(6),
        ]);
    }
}
