<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductImageRepository;
use RuntimeException;

final class ProductImageService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(private ProductImageRepository $images)
    {
    }

    public function uploadForProduct(int $productId, array $file, ?string $altText = null): int
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload immagine non riuscito.');
        }

        $originalName = (string) ($file['name'] ?? '');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new RuntimeException('Formato immagine non supportato.');
        }

        $mimeType = mime_content_type((string) $file['tmp_name']) ?: '';
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new RuntimeException('Il file caricato non e una immagine valida.');
        }

        $uploadDir = BASE_PATH . '/public/upload/products';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Impossibile creare la cartella upload.');
        }

        $targetName = sprintf(
            'product-%d-%d.%s',
            $productId,
            time(),
            $extension
        );
        $targetPath = $uploadDir . '/' . $targetName;

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Impossibile salvare il file caricato.');
        }

        $isPrimary = !$this->images->hasPrimaryImage($productId);

        return $this->images->create([
            'product_id' => $productId,
            'image_path' => 'upload/products/' . $targetName,
            'alt_text' => trim((string) $altText),
            'sort_order' => 0,
            'is_primary' => $isPrimary ? 1 : 0,
        ]);
    }

    public function byProductId(int $productId): array
    {
        return array_map(function (array $image): array {
            $image['public_url'] = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/' . ltrim($image['image_path'], '/');
            return $image;
        }, $this->images->byProductId($productId));
    }

    public function listAvailableLibrary(): array
    {
        $uploadDir = BASE_PATH . '/public/upload/products';
        if (!is_dir($uploadDir)) {
            return [];
        }

        $files = glob($uploadDir . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];
        sort($files);

        return array_map(function (string $path): array {
            $relativePath = 'upload/products/' . basename($path);

            return [
                'file_name' => basename($path),
                'image_path' => $relativePath,
                'public_url' => rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/' . $relativePath,
            ];
        }, $files);
    }

    public function attachExistingToProduct(int $productId, string $imagePath, ?string $altText = null): int
    {
        $normalized = str_replace('\\', '/', trim($imagePath));
        $allowedPrefix = 'upload/products/';
        if (!str_starts_with($normalized, $allowedPrefix)) {
            throw new RuntimeException('Percorso immagine non consentito.');
        }

        $absolutePath = BASE_PATH . '/public/' . $normalized;
        if (!is_file($absolutePath)) {
            throw new RuntimeException('Immagine non trovata nella libreria locale.');
        }

        if ($this->images->existsForProduct($productId, $normalized)) {
            throw new RuntimeException('Questa immagine e gia associata al prodotto.');
        }

        $isPrimary = !$this->images->hasPrimaryImage($productId);

        return $this->images->create([
            'product_id' => $productId,
            'image_path' => $normalized,
            'alt_text' => trim((string) $altText),
            'sort_order' => 0,
            'is_primary' => $isPrimary ? 1 : 0,
        ]);
    }

    public function setPrimaryImage(int $imageId): void
    {
        $image = $this->images->findById($imageId);
        if ($image === null) {
            throw new RuntimeException('Immagine non trovata.');
        }

        $this->images->clearPrimaryForProduct((int) $image['product_id']);
        $this->images->setPrimary($imageId);
    }

    public function removeImage(int $imageId): void
    {
        $image = $this->images->findById($imageId);
        if ($image === null) {
            throw new RuntimeException('Immagine non trovata.');
        }

        $this->images->delete($imageId);

        if ((int) $image['is_primary'] === 1) {
            $remaining = $this->images->byProductId((int) $image['product_id']);
            if ($remaining !== []) {
                $nextPrimaryId = (int) $remaining[0]['id'];
                $this->images->clearPrimaryForProduct((int) $image['product_id']);
                $this->images->setPrimary($nextPrimaryId);
            }
        }
    }

    public function findImageById(int $imageId): ?array
    {
        $image = $this->images->findById($imageId);

        if ($image === null) {
            return null;
        }

        $image['public_url'] = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/' . ltrim($image['image_path'], '/');

        return $image;
    }
}
