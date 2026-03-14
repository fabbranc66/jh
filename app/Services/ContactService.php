<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ContactRequestRepository;
use RuntimeException;

final class ContactService
{
    public function __construct(private ContactRequestRepository $requests)
    {
    }

    public function create(array $input): int
    {
        $name = trim((string) ($input['name'] ?? ''));
        $message = trim((string) ($input['message'] ?? ''));

        if ($name === '' || $message === '') {
            throw new RuntimeException('Nome e messaggio sono obbligatori.');
        }

        return $this->requests->create([
            'name' => $name,
            'email' => trim((string) ($input['email'] ?? '')),
            'phone' => trim((string) ($input['phone'] ?? '')),
            'message' => $message,
            'product_id' => null,
            'source' => 'website',
            'status' => 'new',
        ]);
    }

    public function listAll(array $filters = []): array
    {
        return $this->requests->paginated($filters, 500, 0);
    }

    public function paginate(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $total = $this->requests->count($filters);
        $offset = ($page - 1) * $perPage;

        return [
            'items' => $this->requests->paginated($filters, $perPage, $offset),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function updateStatus(int $id, string $status): void
    {
        $allowed = ['new', 'in_progress', 'done', 'archived'];
        if (!in_array($status, $allowed, true)) {
            throw new RuntimeException('Stato richiesta non valido.');
        }

        $this->requests->updateStatus($id, $status);
    }
}
