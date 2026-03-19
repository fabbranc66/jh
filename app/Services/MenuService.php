<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MenuItemRepository;
use RuntimeException;

final class MenuService
{
    public function __construct(private MenuItemRepository $items)
    {
    }

    public function mainNavigation(): array
    {
        $items = $this->items->allActive();
        $grouped = [];

        foreach ($items as $item) {
            $item['resolved_url'] = $this->resolveUrl((string) $item['url']);
            $item['resolved_image_url'] = $this->resolveImageUrl($item['image_path'] ?? null);
            $item['children'] = [];
            $grouped[(int) $item['id']] = $item;
        }

        foreach (array_keys($grouped) as $id) {
            $parentId = $grouped[$id]['parent_id'] !== null ? (int) $grouped[$id]['parent_id'] : null;

            if ($parentId !== null && isset($grouped[$parentId])) {
                $grouped[$parentId]['children'][] = &$grouped[$id];
            }
        }

        $tree = [];
        foreach ($grouped as $item) {
            if ($item['parent_id'] === null) {
                $tree[] = $item;
            }
        }

        return $tree;
    }

    public function listAll(): array
    {
        return $this->items->allForAdmin();
    }

    public function listParents(): array
    {
        return $this->items->topLevelForAdmin();
    }

    public function findById(int $id): ?array
    {
        return $this->items->findById($id);
    }

    public function create(array $input): int
    {
        return $this->items->create($this->normalize($input));
    }

    public function update(int $id, array $input): void
    {
        $current = $this->items->findById($id);
        if ($current === null) {
            throw new RuntimeException('Voce menu non trovata.');
        }

        $data = $this->normalize($input, $current['image_path'] ?? null);
        if ($data['parent_id'] === $id) {
            throw new RuntimeException('Una voce menu non puo avere se stessa come padre.');
        }

        $this->items->update($id, $data);
    }

    public function updateImage(int $id, array $file): string
    {
        $item = $this->items->findById($id);
        if ($item === null) {
            throw new RuntimeException('Voce menu non trovata.');
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

        $uploadDir = BASE_PATH . '/public/upload/menu';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Impossibile creare la cartella menu.');
        }

        $targetName = sprintf('menu-%d-%d.%s', $id, time(), $extension);
        $targetPath = $uploadDir . '/' . $targetName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Impossibile salvare l immagine caricata.');
        }

        $relativePath = 'upload/menu/' . $targetName;
        $this->items->update($id, $this->normalize([
            'parent_id' => $item['parent_id'] ?? 0,
            'label' => $item['label'],
            'url' => $item['url'],
            'image_path' => $relativePath,
            'sort_order' => $item['sort_order'],
            'is_active' => $item['is_active'] ? '1' : '',
        ], $item['image_path'] ?? null));

        return $relativePath;
    }

    public function moveUp(int $id): void
    {
        $this->move($id, 'up');
    }

    public function moveDown(int $id): void
    {
        $this->move($id, 'down');
    }

    public function delete(int $id): void
    {
        if ($this->items->findById($id) === null) {
            throw new RuntimeException('Voce menu non trovata.');
        }

        $this->items->delete($id);
    }

    private function normalize(array $input, ?string $currentImagePath = null): array
    {
        $label = trim((string) ($input['label'] ?? ''));
        $url = trim((string) ($input['url'] ?? ''));
        $parentId = (int) ($input['parent_id'] ?? 0);

        if ($label === '' || $url === '') {
            throw new RuntimeException('Etichetta e URL sono obbligatori.');
        }

        if (!$this->isValidUrl($url)) {
            throw new RuntimeException('URL menu non valido.');
        }

        return [
            'parent_id' => $parentId > 0 ? $parentId : null,
            'label' => $label,
            'url' => $url,
            'image_path' => trim((string) ($input['image_path'] ?? '')) ?: $currentImagePath,
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'is_active' => isset($input['is_active']) ? 1 : 0,
        ];
    }

    private function isValidUrl(string $value): bool
    {
        return str_starts_with($value, '/') || (bool) filter_var($value, FILTER_VALIDATE_URL);
    }

    private function resolveUrl(string $value): string
    {
        if (str_starts_with($value, '/')) {
            return rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . $value;
        }

        return $value;
    }

    private function resolveImageUrl(?string $value): ?string
    {
        $path = trim((string) $value);
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/' . ltrim($path, '/');
    }

    private function move(int $id, string $direction): void
    {
        $item = $this->items->findById($id);
        if ($item === null) {
            throw new RuntimeException('Voce menu non trovata.');
        }

        $sibling = $direction === 'up'
            ? $this->items->previousSibling($id, $item['parent_id'] !== null ? (int) $item['parent_id'] : null, (int) $item['sort_order'])
            : $this->items->nextSibling($id, $item['parent_id'] !== null ? (int) $item['parent_id'] : null, (int) $item['sort_order']);

        if ($sibling === null) {
            return;
        }

        $this->items->updateSortOrder((int) $item['id'], (int) $sibling['sort_order']);
        $this->items->updateSortOrder((int) $sibling['id'], (int) $item['sort_order']);
    }
}
