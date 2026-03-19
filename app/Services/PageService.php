<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PageRepository;
use RuntimeException;

final class PageService
{
    public function __construct(private PageRepository $pages)
    {
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->pages->findActiveBySlug($slug);
    }

    public function listAll(): array
    {
        return $this->pages->allForAdmin();
    }

    public function findById(int $id): ?array
    {
        return $this->pages->findById($id);
    }

    public function update(int $id, array $input): void
    {
        $page = $this->pages->findById($id);
        if ($page === null) {
            throw new RuntimeException('Pagina non trovata.');
        }

        $this->pages->update($id, $this->normalize($input, $page['image_path'] ?? null));
    }

    public function updateImage(int $id, array $file): string
    {
        $page = $this->pages->findById($id);
        if ($page === null) {
            throw new RuntimeException('Pagina non trovata.');
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload immagine non riuscito.');
        }

        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new RuntimeException('Formato immagine non supportato.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $mimeType = $tmpName !== '' ? (mime_content_type($tmpName) ?: '') : '';
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException('Il file caricato non e una immagine valida.');
        }

        $uploadDir = BASE_PATH . '/public/upload/pages';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Impossibile creare la cartella pagine.');
        }

        $targetName = sprintf('%s-%d.%s', $page['slug'], time(), $extension);
        $targetPath = $uploadDir . '/' . $targetName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Impossibile salvare l immagine caricata.');
        }

        $relativePath = 'upload/pages/' . $targetName;
        $this->pages->update($id, $this->normalize([
            'title' => $page['title'],
            'slug' => $page['slug'],
            'content' => $page['content'],
            'meta_title' => $page['meta_title'],
            'meta_description' => $page['meta_description'],
            'image_path' => $relativePath,
            'is_active' => $page['is_active'] ? '1' : '',
        ], $relativePath));

        return $relativePath;
    }

    public function upsert(array $data): void
    {
        $this->pages->upsertPage($data);
    }

    private function normalize(array $input, ?string $currentImagePath = null): array
    {
        $title = trim((string) ($input['title'] ?? ''));
        $slug = trim((string) ($input['slug'] ?? ''));

        if ($title === '' || $slug === '') {
            throw new RuntimeException('Titolo e slug sono obbligatori.');
        }

        return [
            'title' => $title,
            'slug' => $this->slugify($slug),
            'content' => trim((string) ($input['content'] ?? '')),
            'meta_title' => trim((string) ($input['meta_title'] ?? '')),
            'meta_description' => trim((string) ($input['meta_description'] ?? '')),
            'image_path' => trim((string) ($input['image_path'] ?? '')) ?: $currentImagePath,
            'is_active' => isset($input['is_active']) ? 1 : 0,
        ];
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'pagina';
    }
}
