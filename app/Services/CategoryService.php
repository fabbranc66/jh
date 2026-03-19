<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CategoryRepository;
use RuntimeException;

final class CategoryService
{
    public function __construct(private CategoryRepository $categories)
    {
    }

    public function listAll(): array
    {
        return $this->categories->all();
    }

    public function listActive(): array
    {
        return $this->categories->allActive();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->categories->findActiveBySlug($slug);
    }

    public function findById(int $id): ?array
    {
        return $this->categories->findById($id);
    }

    public function create(array $input): int
    {
        return $this->categories->create($this->normalizeCategoryInput($input));
    }

    public function update(int $id, array $input): void
    {
        $category = $this->categories->findById($id);
        if ($category === null) {
            throw new RuntimeException('Categoria non trovata.');
        }

        $this->categories->update($id, $this->normalizeCategoryInput($input, $category['image'] ?? null));
    }

    public function delete(int $id): void
    {
        if ($this->categories->findById($id) === null) {
            throw new RuntimeException('Categoria non trovata.');
        }

        $this->categories->delete($id);
    }

    public function updateImage(int $id, array $file): string
    {
        $category = $this->categories->findById($id);
        if ($category === null) {
            throw new RuntimeException('Categoria non trovata.');
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

        $uploadDir = BASE_PATH . '/public/upload/categories';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Impossibile creare la cartella categorie.');
        }

        $targetName = sprintf('%s-%d.%s', $category['slug'], time(), $extension);
        $targetPath = $uploadDir . '/' . $targetName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Impossibile salvare l immagine caricata.');
        }

        $relativePath = 'upload/categories/' . $targetName;
        $this->categories->update($id, $this->normalizeCategoryInput([
            'name' => $category['name'],
            'description' => $category['description'],
            'sort_order' => $category['sort_order'],
            'is_active' => $category['is_active'] ? '1' : '',
            'image' => $relativePath,
        ], $relativePath));

        return $relativePath;
    }

    private function normalizeCategoryInput(array $input, ?string $currentImage = null): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('Il nome categoria e obbligatorio.');
        }

        return [
            'name' => $name,
            'slug' => $this->slugify($name),
            'description' => trim((string) ($input['description'] ?? '')),
            'image' => trim((string) ($input['image'] ?? '')) ?: $currentImage,
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'is_active' => isset($input['is_active']) ? 1 : 0,
        ];
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'categoria';
    }
}
