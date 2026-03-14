<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ContactService;
use Throwable;

final class ContactController
{
    public function __construct(
        private View $view,
        private ContactService $contacts
    ) {
    }

    public function index(): void
    {
        $this->view->render('pages/contact.twig', [
            'pageTitle' => 'Contatti',
            'flash' => $this->pullFlash(),
        ]);
    }

    public function submit(): void
    {
        try {
            $this->contacts->create($_POST);
            $this->flash('success', 'Richiesta inviata correttamente.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $baseUrl = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/');
        header('Location: ' . $baseUrl . '/contatti');
        exit;
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
}
