<?php

declare(strict_types=1);

namespace App\Services;

final class AdminAuthService
{
    private const SESSION_KEY = 'admin_auth';
    private const SESSION_TTL = 28800;

    public function isAuthenticated(): bool
    {
        $session = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_array($session) || empty($session['username'])) {
            return false;
        }

        if (($session['logged_in_at'] ?? 0) < (time() - self::SESSION_TTL)) {
            unset($_SESSION[self::SESSION_KEY]);
            return false;
        }

        return true;
    }

    public function username(): string
    {
        return (string) ($_SESSION[self::SESSION_KEY]['username'] ?? '');
    }

    public function attempt(string $username, string $password): bool
    {
        $expectedUsername = (string) ($_ENV['ADMIN_USERNAME'] ?? 'admin');
        $expectedPasswordHash = (string) ($_ENV['ADMIN_PASSWORD_HASH'] ?? '');
        $expectedPassword = (string) ($_ENV['ADMIN_PASSWORD'] ?? '');

        if ($expectedPasswordHash === '' && $expectedPassword === '') {
            return false;
        }

        if (!hash_equals($expectedUsername, trim($username))) {
            return false;
        }

        $isValid = $expectedPasswordHash !== ''
            ? password_verify($password, $expectedPasswordHash)
            : hash_equals($expectedPassword, $password);

        if (!$isValid) {
            return false;
        }

        session_regenerate_id(true);

        $_SESSION[self::SESSION_KEY] = [
            'username' => $expectedUsername,
            'logged_in_at' => time(),
        ];

        return true;
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }
}
