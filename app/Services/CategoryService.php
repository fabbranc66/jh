<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CategoryRepository;

final class CategoryService
{
    public function __construct(private CategoryRepository $categories)
    {
    }

    public function listActive(): array
    {
        return $this->categories->allActive();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->categories->findActiveBySlug($slug);
    }
}
