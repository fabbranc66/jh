<?php

declare(strict_types=1);

use App\Controllers\AdminAuthController;
use App\Controllers\AdminController;
use App\Controllers\CatalogController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\ProductController;
use App\Repositories\CategoryRepository;
use App\Repositories\ContactRequestRepository;
use App\Repositories\PageRepository;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Services\AdminAuthService;
use App\Services\CategoryService;
use App\Services\ContactService;
use App\Services\PageService;
use App\Services\ProductImageService;
use App\Services\ProductService;

$categoryService = new CategoryService(new CategoryRepository($flight->get('db')));
$contactService = new ContactService(new ContactRequestRepository($flight->get('db')));
$productImageRepository = new ProductImageRepository($flight->get('db'));
$productService = new ProductService(new ProductRepository($flight->get('db')), $productImageRepository);
$productImageService = new ProductImageService($productImageRepository);
$pageService = new PageService(new PageRepository($flight->get('db')));
$adminAuth = $flight->get('adminAuth');
$adminController = new AdminController($view, $adminAuth, $categoryService, $productService, $productImageService, $contactService);
$adminAuthController = new AdminAuthController($view, $adminAuth);
$contactController = new ContactController($view, $contactService);

$flight->route('GET /', [new HomeController($view, $categoryService, $productService), 'index']);
$flight->route('GET /catalogo', [new CatalogController($view, $categoryService, $productService), 'index']);
$flight->route('GET /categoria/@slug', [new CatalogController($view, $categoryService, $productService), 'category']);
$flight->route('GET /prodotto/@slug', [new ProductController($view, $productService, $productImageService), 'show']);
$pageController = new PageController($view, $pageService);

$flight->route('GET /laboratorio', static function () use ($pageController): void {
    $pageController->show('laboratorio');
});
$flight->route('GET /admin/login', [$adminAuthController, 'login']);
$flight->route('POST /admin/login', [$adminAuthController, 'authenticate']);
$flight->route('POST /admin/logout', [$adminAuthController, 'logout']);
$flight->route('GET /admin', [$adminController, 'dashboard']);
$flight->route('GET /admin/categorie', [$adminController, 'categories']);
$flight->route('GET /admin/categorie/@categoryId/modifica', [$adminController, 'editCategory']);
$flight->route('POST /admin/categorie', [$adminController, 'storeCategory']);
$flight->route('POST /admin/categorie/@categoryId/modifica', [$adminController, 'updateCategory']);
$flight->route('POST /admin/categorie/@categoryId/elimina', [$adminController, 'deleteCategory']);
$flight->route('GET /admin/prodotti', [$adminController, 'products']);
$flight->route('GET /admin/prodotti/nuovo', [$adminController, 'newProduct']);
$flight->route('POST /admin/prodotti', [$adminController, 'storeProduct']);
$flight->route('GET /admin/prodotti/@productId/modifica', [$adminController, 'editProduct']);
$flight->route('POST /admin/prodotti/@productId/modifica', [$adminController, 'updateProduct']);
$flight->route('POST /admin/prodotti/@productId/elimina', [$adminController, 'deleteProduct']);
$flight->route('POST /admin/prodotti/@productId/immagini', [$adminController, 'uploadProductImage']);
$flight->route('POST /admin/prodotti/@productId/immagini/esistenti', [$adminController, 'attachExistingProductImage']);
$flight->route('POST /admin/immagini/@imageId/principale', [$adminController, 'makePrimaryProductImage']);
$flight->route('POST /admin/immagini/@imageId/rimuovi', [$adminController, 'removeProductImage']);
$flight->route('GET /admin/contatti', [$adminController, 'contacts']);
$flight->route('POST /admin/contatti/@requestId/stato', [$adminController, 'updateContactStatus']);
$flight->route('GET /contatti', [$contactController, 'index']);
$flight->route('POST /contatti', [$contactController, 'submit']);
