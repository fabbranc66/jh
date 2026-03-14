<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\AdminAuthService;

final class AdminAuthController
{
    public function __construct(
        private View $view,
        private AdminAuthService $auth
    ) {
    }

    public function login(): void
    {
        if ($this->auth->isAuthenticated()) {
            $this->redirect('/admin');
        }

        $this->view->render('pages/admin/login.twig', [
            'pageTitle' => 'Login admin',
            'flash' => $this->pullFlash(),
        ]);
    }

    public function authenticate(): void
    {
        $username = (string) ($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($this->auth->attempt($username, $password)) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Accesso effettuato.',
            ];
            $this->redirect('/admin');
        }

        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Credenziali non valide.',
        ];
        $this->redirect('/admin/login');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Sessione admin chiusa.',
        ];
        $this->redirect('/admin/login');
    }

    private function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return is_array($flash) ? $flash : null;
    }

    private function redirect(string $path): void
    {
        $baseUrl = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/');
        header('Location: ' . $baseUrl . $path);
        exit;
    }
}
