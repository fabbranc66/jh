<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\AdminAuthService;
use App\Services\CategoryService;
use App\Services\ContactService;
use App\Services\ProductImageService;
use App\Services\ProductService;
use Throwable;

final class AdminController
{
    public function __construct(
        private View $view,
        private AdminAuthService $auth,
        private CategoryService $categories,
        private ProductService $products,
        private ProductImageService $images,
        private ContactService $contacts
    ) {
    }

    public function dashboard(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/dashboard.twig', [
            'pageTitle' => 'Admin',
            'categories' => $this->categories->listAll(),
            'products' => $this->products->listAllForAdmin(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function categories(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/categories.twig', [
            'pageTitle' => 'Admin categorie',
            'categories' => $this->categories->listAll(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function editCategory(string $categoryId): void
    {
        $this->requireAuth();

        $category = $this->categories->findById((int) $categoryId);
        if ($category === null) {
            $this->flash('error', 'Categoria non trovata.');
            $this->redirect('/admin/categorie');
        }

        $this->view->render('pages/admin/category-edit.twig', [
            'pageTitle' => 'Modifica categoria',
            'category' => $category,
            'flash' => $this->pullFlash(),
        ]);
    }

    public function storeCategory(): void
    {
        $this->requireAuth();

        try {
            $this->categories->create($_POST);
            $this->flash('success', 'Categoria creata correttamente.');
        } catch (Throwable $exception) {
            $this->flash('error', 'Impossibile creare la categoria. Verifica nome e slug univoco.');
        }

        $this->redirect('/admin/categorie');
    }

    public function updateCategory(string $categoryId): void
    {
        $this->requireAuth();

        try {
            $this->categories->update((int) $categoryId, $_POST);
            $this->flash('success', 'Categoria aggiornata correttamente.');
        } catch (Throwable $exception) {
            $this->flash('error', 'Impossibile aggiornare la categoria.');
        }

        $this->redirect('/admin/categorie/' . (int) $categoryId . '/modifica');
    }

    public function products(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/products.twig', [
            'pageTitle' => 'Admin prodotti',
            'products' => $this->products->listAllForAdmin(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function newProduct(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/product-new.twig', [
            'pageTitle' => 'Nuovo prodotto',
            'categories' => $this->categories->listAll(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function storeProduct(): void
    {
        $this->requireAuth();

        try {
            $this->products->create($_POST);
            $this->flash('success', 'Prodotto creato correttamente.');
            $this->redirect('/admin/prodotti');
        } catch (Throwable $exception) {
            $this->flash('error', 'Impossibile creare il prodotto. Controlla categoria, nome e slug univoco.');
            $this->redirect('/admin/prodotti/nuovo');
        }
    }

    public function editProduct(string $productId): void
    {
        $this->requireAuth();

        $product = $this->products->findById((int) $productId);
        if ($product === null) {
            $this->flash('error', 'Prodotto non trovato.');
            $this->redirect('/admin/prodotti');
        }

        $this->view->render('pages/admin/product-edit.twig', [
            'pageTitle' => 'Modifica prodotto',
            'product' => $product,
            'categories' => $this->categories->listAll(),
            'images' => $this->images->byProductId((int) $product['id']),
            'imageLibrary' => $this->images->listAvailableLibrary(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function updateProduct(string $productId): void
    {
        $this->requireAuth();

        try {
            $this->products->update((int) $productId, $_POST);
            $this->flash('success', 'Prodotto aggiornato correttamente.');
            $this->redirect('/admin/prodotti/' . (int) $productId . '/modifica');
        } catch (Throwable $exception) {
            $this->flash('error', 'Impossibile aggiornare il prodotto. Controlla i dati inseriti.');
            $this->redirect('/admin/prodotti/' . (int) $productId . '/modifica');
        }
    }

    public function deleteProduct(string $productId): void
    {
        $this->requireAuth();

        try {
            $this->products->delete((int) $productId);
            $this->flash('success', 'Prodotto eliminato.');
        } catch (Throwable $exception) {
            $this->flash('error', 'Impossibile eliminare il prodotto.');
        }

        $this->redirect('/admin/prodotti');
    }

    public function uploadProductImage(string $productId): void
    {
        $this->requireAuth();

        try {
            $productIdValue = (int) $productId;
            $product = $this->products->findById($productIdValue);
            if ($product === null) {
                throw new \RuntimeException('Prodotto non trovato.');
            }

            $this->images->uploadForProduct(
                $productIdValue,
                $_FILES['image'] ?? [],
                (string) ($_POST['alt_text'] ?? $product['name'])
            );
            $this->flash('success', 'Immagine caricata correttamente.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/prodotti/' . $productIdValue . '/modifica');
    }

    public function attachExistingProductImage(string $productId): void
    {
        $this->requireAuth();

        try {
            $productIdValue = (int) $productId;
            $product = $this->products->findById($productIdValue);
            if ($product === null) {
                throw new \RuntimeException('Prodotto non trovato.');
            }

            $this->images->attachExistingToProduct(
                $productIdValue,
                (string) ($_POST['image_path'] ?? ''),
                (string) ($_POST['alt_text'] ?? $product['name'])
            );

            $this->flash('success', 'Immagine esistente associata correttamente.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/prodotti/' . $productIdValue . '/modifica');
    }

    public function makePrimaryProductImage(string $imageId): void
    {
        $this->requireAuth();

        $image = $this->images->findImageById((int) $imageId);
        if ($image === null) {
            $this->flash('error', 'Immagine non trovata.');
            $this->redirect('/admin/prodotti');
        }

        try {
            $this->images->setPrimaryImage((int) $imageId);
            $this->flash('success', 'Immagine principale aggiornata.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/prodotti/' . (int) $image['product_id'] . '/modifica');
    }

    public function removeProductImage(string $imageId): void
    {
        $this->requireAuth();

        $image = $this->images->findImageById((int) $imageId);
        if ($image === null) {
            $this->flash('error', 'Immagine non trovata.');
            $this->redirect('/admin/prodotti');
        }

        try {
            $this->images->removeImage((int) $imageId);
            $this->flash('success', 'Immagine rimossa dal prodotto.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/prodotti/' . (int) $image['product_id'] . '/modifica');
    }

    public function contacts(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/contacts.twig', [
            'pageTitle' => 'Richieste contatto',
            'requests' => $this->contacts->listAll(),
            'flash' => $this->pullFlash(),
        ]);
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    private function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return is_array($flash) ? $flash : null;
    }

    private function requireAuth(): void
    {
        if ($this->auth->isAuthenticated()) {
            return;
        }

        $this->flash('error', 'Devi effettuare il login per accedere all area admin.');
        $this->redirect('/admin/login');
    }

    private function redirect(string $path): void
    {
        $baseUrl = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/');
        header('Location: ' . $baseUrl . $path);
        exit;
    }
}
