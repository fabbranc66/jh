<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\HomeSlideRepository;
use RuntimeException;

final class HomeSlideService
{
    public function __construct(private HomeSlideRepository $slides)
    {
    }

    public function listActive(): array
    {
        return array_map(function (array $slide): array {
            $slide['resolved_link_url'] = $this->resolveLink($slide['link_url']);
            return $slide;
        }, $this->slides->allActive());
    }

    public function listAll(): array
    {
        return $this->slides->allForAdmin();
    }

    public function findById(int $id): ?array
    {
        return $this->slides->findById($id);
    }

    public function create(array $input): int
    {
        return $this->slides->create($this->normalize($input));
    }

    public function update(int $id, array $input): void
    {
        if ($this->slides->findById($id) === null) {
            throw new RuntimeException('Slide non trovata.');
        }

        $this->slides->update($id, $this->normalize($input));
    }

    public function delete(int $id): void
    {
        if ($this->slides->findById($id) === null) {
            throw new RuntimeException('Slide non trovata.');
        }

        $this->slides->delete($id);
    }

    private function normalize(array $input): array
    {
        $title = trim((string) ($input['title'] ?? ''));
        $imageUrl = trim((string) ($input['image_url'] ?? ''));
        $linkUrl = trim((string) ($input['link_url'] ?? ''));

        if ($title === '' || $imageUrl === '' || $linkUrl === '') {
            throw new RuntimeException('Titolo, immagine e link sono obbligatori.');
        }

        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('URL immagine non valido.');
        }

        if (!$this->isValidLink($linkUrl)) {
            throw new RuntimeException('Link slide non valido.');
        }

        return [
            'title' => $title,
            'subtitle' => trim((string) ($input['subtitle'] ?? '')),
            'image_url' => $imageUrl,
            'link_url' => $linkUrl,
            'button_label' => trim((string) ($input['button_label'] ?? 'Scopri')) ?: 'Scopri',
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'is_active' => isset($input['is_active']) ? 1 : 0,
        ];
    }

    private function isValidLink(string $value): bool
    {
        return str_starts_with($value, '/') || (bool) filter_var($value, FILTER_VALIDATE_URL);
    }

    private function resolveLink(string $value): string
    {
        if (str_starts_with($value, '/')) {
            return rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . $value;
        }

        return $value;
    }
}
