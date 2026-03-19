<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SettingRepository;
use RuntimeException;

final class BrandingService
{
    private const SETTING_LOGO_PATH = 'site_logo_path';
    private const FALLBACK_LOGO_PATH = 'assets/images/logo_jh.png';
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(private SettingRepository $settings)
    {
    }

    public function globals(): array
    {
        $logoPath = $this->logoPath();

        return [
            'logo_path' => $logoPath,
            'logo_url' => rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/' . ltrim($logoPath, '/'),
        ];
    }

    public function logoPath(): string
    {
        $value = trim((string) ($this->settings->findValue(self::SETTING_LOGO_PATH) ?? ''));

        return $value !== '' ? $value : self::FALLBACK_LOGO_PATH;
    }

    public function updateLogo(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload logo non riuscito.');
        }

        $originalName = (string) ($file['name'] ?? '');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new RuntimeException('Formato logo non supportato.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $mimeType = $tmpName !== '' ? (mime_content_type($tmpName) ?: '') : '';
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new RuntimeException('Il file caricato non e una immagine valida.');
        }

        $uploadDir = BASE_PATH . '/public/upload/branding';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Impossibile creare la cartella branding.');
        }

        $targetName = sprintf('logo-%d.%s', time(), $extension);
        $targetPath = $uploadDir . '/' . $targetName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Impossibile salvare il logo caricato.');
        }

        $relativePath = 'upload/branding/' . $targetName;
        $this->settings->upsert(self::SETTING_LOGO_PATH, $relativePath);

        return $relativePath;
    }
}
