<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ContactService;
use App\Services\PageService;
use Throwable;

final class ContactController
{
    public function __construct(
        private View $view,
        private ContactService $contacts,
        private PageService $pages
    ) {
    }

    public function index(): void
    {
        $page = $this->pages->findBySlug('contatti');

        $this->view->render('pages/contact.twig', [
            'pageTitle' => 'Contatti',
            'page' => $page,
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    public function submit(): void
    {
        try {
            $this->contacts->create($_POST, $_SERVER);
            $this->flash('success', 'Richiesta inviata correttamente.');
            $this->clearOld();
        } catch (Throwable $exception) {
            $this->rememberOld($_POST);
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

    private function rememberOld(array $input): void
    {
        $_SESSION['contact_old'] = $input;
    }

    private function pullOld(): array
    {
        $old = $_SESSION['contact_old'] ?? [];
        unset($_SESSION['contact_old']);

        return is_array($old) ? $old : [];
    }

    private function clearOld(): void
    {
        unset($_SESSION['contact_old']);
    }
}
