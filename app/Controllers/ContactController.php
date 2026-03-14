<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class ContactController
{
    public function __construct(private View $view)
    {
    }

    public function index(): void
    {
        $this->view->render('pages/contact.twig', [
            'pageTitle' => 'Contatti',
        ]);
    }

    public function submit(): void
    {
        header('Location: /jh/public/contatti');
        exit;
    }
}
