<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\AdminAuthService;
use App\Services\BrandingService;
use App\Services\CategoryService;
use App\Services\ComponentService;
use App\Services\ContactService;
use App\Services\HomeSlideService;
use App\Services\MenuService;
use App\Services\PageService;
use App\Services\ProductBomService;
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
        private ContactService $contacts,
        private ComponentService $components,
        private ProductBomService $bom,
        private HomeSlideService $slides,
        private BrandingService $branding,
        private MenuService $menu,
        private PageService $pages
    ) {
    }

    public function dashboard(): void
    {
        $this->requireAuth();

        $products = $this->products->paginateForAdmin([], 1, 1);
        $contacts = $this->contacts->paginate([], 1, 1);

        $this->view->render('pages/admin/dashboard.twig', [
            'pageTitle' => 'Admin',
            'categories' => $this->categories->listAll(),
            'productsCount' => $products['total'],
            'contactsCount' => $contacts['total'],
            'componentsCount' => $this->components->paginateForAdmin([], 1, 1)['total'],
            'reorderCount' => $this->components->countReorderNeeded(),
            'slidesCount' => count($this->slides->listAll()),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function slides(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/slides.twig', [
            'pageTitle' => 'Slide home',
            'slides' => $this->slides->listAll(),
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    public function branding(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/branding.twig', [
            'pageTitle' => 'Branding',
            'branding' => $this->branding->globals(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function menu(): void
    {
        $this->requireAuth();

        $parents = $this->menu->listParents();
        $parentLabels = [];
        foreach ($parents as $parent) {
            $parentLabels[(int) $parent['id']] = (string) $parent['label'];
        }

        $items = array_map(function (array $item) use ($parentLabels): array {
            $item['parent_label'] = $item['parent_id'] !== null ? ($parentLabels[(int) $item['parent_id']] ?? '') : null;
            $item['item_type'] = $item['parent_id'] !== null ? 'Sottocategoria' : 'Categoria';
            return $item;
        }, $this->menu->listAll());

        $groupedItems = [];
        foreach ($items as $item) {
            if ($item['parent_id'] === null) {
                $groupedItems[] = $item;

                foreach ($items as $child) {
                    if ((int) ($child['parent_id'] ?? 0) === (int) $item['id']) {
                        $groupedItems[] = $child;
                    }
                }
            }
        }

        foreach ($items as $item) {
            $alreadyIncluded = false;
            foreach ($groupedItems as $included) {
                if ((int) $included['id'] === (int) $item['id']) {
                    $alreadyIncluded = true;
                    break;
                }
            }

            if (!$alreadyIncluded) {
                $groupedItems[] = $item;
            }
        }

        $this->view->render('pages/admin/menu.twig', [
            'pageTitle' => 'Menu sito',
            'items' => $groupedItems,
            'parents' => $parents,
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    public function pages(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/pages.twig', [
            'pageTitle' => 'Pagine',
            'pages' => $this->pages->listAll(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function editPage(string $pageId): void
    {
        $this->requireAuth();

        $page = $this->pages->findById((int) $pageId);
        if ($page === null) {
            $this->flash('error', 'Pagina non trovata.');
            $this->redirect('/admin/pagine');
        }

        $this->view->render('pages/admin/page-edit.twig', [
            'pageTitle' => 'Modifica pagina',
            'pageItem' => $page,
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    public function updatePage(string $pageId): void
    {
        $this->requireAuth();

        try {
            $this->pages->update((int) $pageId, $_POST);
            $this->flash('success', 'Pagina aggiornata correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/pagine/' . (int) $pageId . '/modifica');
    }

    public function uploadPageImage(string $pageId): void
    {
        $this->requireAuth();

        try {
            $this->pages->updateImage((int) $pageId, $_FILES['image'] ?? []);
            $this->flash('success', 'Immagine pagina aggiornata.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/pagine/' . (int) $pageId . '/modifica');
    }

    public function editMenuItem(string $itemId): void
    {
        $this->requireAuth();

        $item = $this->menu->findById((int) $itemId);
        if ($item === null) {
            $this->flash('error', 'Voce menu non trovata.');
            $this->redirect('/admin/menu');
        }

        $this->view->render('pages/admin/menu-edit.twig', [
            'pageTitle' => 'Modifica voce menu',
            'item' => $item,
            'parents' => $this->menu->listParents(),
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    public function storeMenuItem(): void
    {
        $this->requireAuth();

        try {
            $this->menu->create($_POST);
            $this->flash('success', 'Voce menu creata correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/menu');
    }

    public function updateMenuItem(string $itemId): void
    {
        $this->requireAuth();

        try {
            $this->menu->update((int) $itemId, $_POST);
            $this->flash('success', 'Voce menu aggiornata correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/menu/' . (int) $itemId . '/modifica');
    }

    public function uploadMenuItemImage(string $itemId): void
    {
        $this->requireAuth();

        try {
            $this->menu->updateImage((int) $itemId, $_FILES['image'] ?? []);
            $this->flash('success', 'Immagine sottocategoria aggiornata.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/menu/' . (int) $itemId . '/modifica');
    }

    public function moveMenuItemUp(string $itemId): void
    {
        $this->requireAuth();

        try {
            $this->menu->moveUp((int) $itemId);
            $this->flash('success', 'Elemento menu spostato verso l alto.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/menu');
    }

    public function moveMenuItemDown(string $itemId): void
    {
        $this->requireAuth();

        try {
            $this->menu->moveDown((int) $itemId);
            $this->flash('success', 'Elemento menu spostato verso il basso.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/menu');
    }

    public function deleteMenuItem(string $itemId): void
    {
        $this->requireAuth();

        try {
            $this->menu->delete((int) $itemId);
            $this->flash('success', 'Voce menu eliminata.');
            $this->redirect('/admin/menu');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/menu/' . (int) $itemId . '/modifica');
        }
    }

    public function updateBrandingLogo(): void
    {
        $this->requireAuth();

        try {
            $this->branding->updateLogo($_FILES['logo'] ?? []);
            $this->flash('success', 'Logo aggiornato correttamente.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/branding');
    }

    public function editSlide(string $slideId): void
    {
        $this->requireAuth();

        $slide = $this->slides->findById((int) $slideId);
        if ($slide === null) {
            $this->flash('error', 'Slide non trovata.');
            $this->redirect('/admin/slides');
        }

        $this->view->render('pages/admin/slide-edit.twig', [
            'pageTitle' => 'Modifica slide',
            'slide' => $slide,
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    public function storeSlide(): void
    {
        $this->requireAuth();

        try {
            $this->slides->create($_POST);
            $this->flash('success', 'Slide creata correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/slides');
    }

    public function updateSlide(string $slideId): void
    {
        $this->requireAuth();

        try {
            $this->slides->update((int) $slideId, $_POST);
            $this->flash('success', 'Slide aggiornata correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/slides/' . (int) $slideId . '/modifica');
    }

    public function deleteSlide(string $slideId): void
    {
        $this->requireAuth();

        try {
            $this->slides->delete((int) $slideId);
            $this->flash('success', 'Slide eliminata.');
            $this->redirect('/admin/slides');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/slides/' . (int) $slideId . '/modifica');
        }
    }

    public function categories(): void
    {
        $this->requireAuth();

        $this->view->render('pages/admin/categories.twig', [
            'pageTitle' => 'Admin categorie',
            'categories' => $this->categories->listAll(),
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
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
            'old' => $this->pullOld(),
        ]);
    }

    public function storeCategory(): void
    {
        $this->requireAuth();

        try {
            $this->categories->create($_POST);
            $this->flash('success', 'Categoria creata correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/categorie');
    }

    public function updateCategory(string $categoryId): void
    {
        $this->requireAuth();

        try {
            $this->categories->update((int) $categoryId, $_POST);
            $this->flash('success', 'Categoria aggiornata correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/categorie/' . (int) $categoryId . '/modifica');
    }

    public function uploadCategoryImage(string $categoryId): void
    {
        $this->requireAuth();

        try {
            $this->categories->updateImage((int) $categoryId, $_FILES['image'] ?? []);
            $this->flash('success', 'Immagine categoria aggiornata.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/categorie/' . (int) $categoryId . '/modifica');
    }

    public function deleteCategory(string $categoryId): void
    {
        $this->requireAuth();

        try {
            $this->categories->delete((int) $categoryId);
            $this->flash('success', 'Categoria eliminata.');
            $this->redirect('/admin/categorie');
        } catch (Throwable $exception) {
            $this->flash('error', 'Impossibile eliminare la categoria. Verifica che non abbia prodotti collegati.');
            $this->redirect('/admin/categorie/' . (int) $categoryId . '/modifica');
        }
    }

    public function products(): void
    {
        $this->requireAuth();

        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'category_id' => (int) ($_GET['category_id'] ?? 0),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pagination = $this->products->paginateForAdmin($filters, $page, 20);

        $this->view->render('pages/admin/products.twig', [
            'pageTitle' => 'Admin prodotti',
            'products' => $pagination['items'],
            'pagination' => $this->buildPagination('/admin/prodotti', $filters, $pagination),
            'categories' => $this->categories->listAll(),
            'filters' => $filters,
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
            'old' => $this->pullOld(),
        ]);
    }

    public function storeProduct(): void
    {
        $this->requireAuth();

        try {
            $this->products->create($_POST);
            $this->flash('success', 'Prodotto creato correttamente.');
            $this->clearOld();
            $this->redirect('/admin/prodotti');
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
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
            'components' => $this->components->listActive(),
            'bomItems' => $this->bom->byProductId((int) $product['id']),
            'images' => $this->images->byProductId((int) $product['id']),
            'imageLibrary' => $this->images->listAvailableLibrary(),
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    public function updateProduct(string $productId): void
    {
        $this->requireAuth();

        try {
            $this->products->update((int) $productId, $_POST);
            $this->flash('success', 'Prodotto aggiornato correttamente.');
            $this->clearOld();
            $this->redirect('/admin/prodotti/' . (int) $productId . '/modifica');
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
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

    public function storeBomItem(string $productId): void
    {
        $this->requireAuth();

        try {
            $this->bom->addItem((int) $productId, $_POST);
            $this->flash('success', 'Voce distinta base aggiunta.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/prodotti/' . (int) $productId . '/modifica');
    }

    public function deleteBomItem(string $bomItemId): void
    {
        $this->requireAuth();

        try {
            $item = $this->bom->removeItem((int) $bomItemId);
            $this->flash('success', 'Voce distinta base rimossa.');
            $this->redirect('/admin/prodotti/' . (int) $item['product_id'] . '/modifica');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/prodotti');
        }
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

        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pagination = $this->contacts->paginate($filters, $page, 20);

        $this->view->render('pages/admin/contacts.twig', [
            'pageTitle' => 'Richieste contatto',
            'requests' => $pagination['items'],
            'pagination' => $this->buildPagination('/admin/contatti', $filters, $pagination),
            'filters' => $filters,
            'flash' => $this->pullFlash(),
        ]);
    }

    public function components(): void
    {
        $this->requireAuth();

        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'stock' => trim((string) ($_GET['stock'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pagination = $this->components->paginateForAdmin($filters, $page, 20);

        $this->view->render('pages/admin/components.twig', [
            'pageTitle' => 'Componenti',
            'components' => $pagination['items'],
            'pagination' => $this->buildPagination('/admin/componenti', $filters, $pagination),
            'filters' => $filters,
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
            'reorderCount' => $this->components->countReorderNeeded(),
        ]);
    }

    public function editComponent(string $componentId): void
    {
        $this->requireAuth();

        $component = $this->components->findById((int) $componentId);
        if ($component === null) {
            $this->flash('error', 'Componente non trovato.');
            $this->redirect('/admin/componenti');
        }

        $this->view->render('pages/admin/component-edit.twig', [
            'pageTitle' => 'Modifica componente',
            'component' => $component,
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    public function storeComponent(): void
    {
        $this->requireAuth();

        try {
            $this->components->create($_POST);
            $this->flash('success', 'Componente creato correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/componenti');
    }

    public function updateComponent(string $componentId): void
    {
        $this->requireAuth();

        try {
            $this->components->update((int) $componentId, $_POST);
            $this->flash('success', 'Componente aggiornato correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/componenti/' . (int) $componentId . '/modifica');
    }

    public function deleteComponent(string $componentId): void
    {
        $this->requireAuth();

        try {
            $this->components->delete((int) $componentId);
            $this->flash('success', 'Componente eliminato.');
            $this->redirect('/admin/componenti');
        } catch (Throwable $exception) {
            $this->flash('error', 'Impossibile eliminare il componente. Verifica che non sia usato in una distinta base.');
            $this->redirect('/admin/componenti/' . (int) $componentId . '/modifica');
        }
    }

    public function updateContactStatus(string $requestId): void
    {
        $this->requireAuth();

        try {
            $this->contacts->updateStatus((int) $requestId, (string) ($_POST['status'] ?? ''));
            $this->flash('success', 'Stato richiesta aggiornato.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/contatti');
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

    private function rememberOld(array $input): void
    {
        $_SESSION['old'] = $input;
    }

    private function pullOld(): array
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);

        return is_array($old) ? $old : [];
    }

    private function clearOld(): void
    {
        unset($_SESSION['old']);
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

    private function buildPagination(string $path, array $filters, array $pagination): array
    {
        if (($pagination['pages'] ?? 1) <= 1) {
            return [];
        }

        $page = (int) $pagination['page'];
        $pages = (int) $pagination['pages'];
        $start = max(1, $page - 2);
        $end = min($pages, $page + 2);
        $links = [];

        for ($current = $start; $current <= $end; $current++) {
            $links[] = [
                'page' => $current,
                'url' => $this->buildPageUrl($path, $filters, $current),
                'is_current' => $current === $page,
            ];
        }

        return [
            'total' => $pagination['total'],
            'page' => $page,
            'pages' => $pages,
            'prev_url' => $page > 1 ? $this->buildPageUrl($path, $filters, $page - 1) : null,
            'next_url' => $page < $pages ? $this->buildPageUrl($path, $filters, $page + 1) : null,
            'links' => $links,
        ];
    }

    private function buildPageUrl(string $path, array $filters, int $page): string
    {
        $query = array_filter(
            array_merge($filters, ['page' => $page]),
            static fn (mixed $value): bool => $value !== '' && $value !== 0 && $value !== null
        );

        $baseUrl = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/');

        return $baseUrl . $path . ($query ? '?' . http_build_query($query) : '');
    }
}
