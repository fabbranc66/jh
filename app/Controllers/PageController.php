<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\PageService;

final class PageController
{
    public function __construct(
        private View $view,
        private PageService $pages
    ) {
    }

    public function show(string $slug): void
    {
        $page = $this->pages->findBySlug($slug);

        if ($page === null) {
            http_response_code(404);

            $this->view->render('pages/not-found.twig', [
                'pageTitle' => 'Pagina non trovata',
                'message' => 'La pagina richiesta non e disponibile.',
            ]);
            return;
        }

        $this->view->render('pages/page.twig', [
            'pageTitle' => $page['meta_title'] ?: $page['title'],
            'page' => $page,
        ]);
    }
}
