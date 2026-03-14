<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PageRepository;

final class PageService
{
    public function __construct(private PageRepository $pages)
    {
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->pages->findActiveBySlug($slug);
    }
}
