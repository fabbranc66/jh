<?php

declare(strict_types=1);

namespace App\Services;

final class CsrfService
{
    private const SESSION_KEY = '_csrf_token';

    public function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[self::SESSION_KEY];
    }

    public function validate(?string $submittedToken): bool
    {
        $sessionToken = (string) ($_SESSION[self::SESSION_KEY] ?? '');

        if ($sessionToken === '' || $submittedToken === null || $submittedToken === '') {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }
}
