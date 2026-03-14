<?php

declare(strict_types=1);

namespace App\Services;

final class AdminAuthService
{
    public function isAuthenticated(): bool
    {
        return !empty($_SESSION['admin_auth']);
    }

    public function username(): string
    {
        return (string) ($_SESSION['admin_auth']['username'] ?? '');
    }

    public function attempt(string $username, string $password): bool
    {
        $expectedUsername = (string) ($_ENV['ADMIN_USERNAME'] ?? 'admin');
        $expectedPassword = (string) ($_ENV['ADMIN_PASSWORD'] ?? '');

        if ($expectedPassword === '') {
            return false;
        }

        if (!hash_equals($expectedUsername, trim($username))) {
            return false;
        }

        if (!hash_equals($expectedPassword, $password)) {
            return false;
        }

        $_SESSION['admin_auth'] = [
            'username' => $expectedUsername,
            'logged_in_at' => time(),
        ];

        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['admin_auth']);
    }
}
