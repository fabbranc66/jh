<?php

declare(strict_types=1);

use App\Controllers\CatalogController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\ProductController;
use App\Repositories\CategoryRepository;
use App\Repositories\PageRepository;
use App\Repositories\ProductRepository;
use App\Services\CategoryService;
use App\Services\PageService;
use App\Services\ProductService;

$categoryService = new CategoryService(new CategoryRepository($flight->get('db')));
$productService = new ProductService(new ProductRepository($flight->get('db')));
$pageService = new PageService(new PageRepository($flight->get('db')));

$flight->route('GET /', [new HomeController($view, $categoryService, $productService), 'index']);
$flight->route('GET /catalogo', [new CatalogController($view, $categoryService, $productService), 'index']);
$flight->route('GET /categoria/@slug', [new CatalogController($view, $categoryService, $productService), 'category']);
$flight->route('GET /prodotto/@slug', [new ProductController($view, $productService), 'show']);
$pageController = new PageController($view, $pageService);

$flight->route('GET /laboratorio', static function () use ($pageController): void {
    $pageController->show('laboratorio');
});
$flight->route('GET /contatti', [new ContactController($view), 'index']);
$flight->route('POST /contatti', [new ContactController($view), 'submit']);
