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

    public function listAll(): array
    {
        return $this->requests->all();
    }
}
