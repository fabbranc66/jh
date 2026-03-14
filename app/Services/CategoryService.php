<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CategoryRepository;

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
        if ($this->categories->findById($id) === null) {
            throw new \RuntimeException('Categoria non trovata.');
        }

        $this->categories->update($id, $this->normalizeCategoryInput($input));
    }

    public function delete(int $id): void
    {
        if ($this->categories->findById($id) === null) {
            throw new \RuntimeException('Categoria non trovata.');
        }

        $this->categories->delete($id);
    }

    private function normalizeCategoryInput(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new \RuntimeException('Il nome categoria e obbligatorio.');
        }

        return [
            'name' => $name,
            'slug' => $this->slugify($name),
            'description' => trim((string) ($input['description'] ?? '')),
            'image' => null,
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
