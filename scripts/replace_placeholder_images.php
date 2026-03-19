<?php

declare(strict_types=1);

$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=Sql1874742_3;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$categoryImages = [
    'gioielli-wire' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=1200&q=80',
    'gioielli-resina' => 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?auto=format&fit=crop&w=1200&q=80',
    'gioielli-ibridi' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=1200&q=80',
    'oggettistica-resina' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?auto=format&fit=crop&w=1200&q=80',
    'stampa-3d' => 'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?auto=format&fit=crop&w=1200&q=80',
    'smart-objects' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
    'segnaletica-personalizzata' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1200&q=80',
    'eventi' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80',
    'pet' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=1200&q=80',
    'scanner-3d' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=80',
    'reverse-engineering' => 'https://images.unsplash.com/photo-1581092334651-ddf26d9a09d0?auto=format&fit=crop&w=1200&q=80',
    'accessori-vape' => 'https://images.unsplash.com/photo-1517705008128-361805f42e86?auto=format&fit=crop&w=1200&q=80',
];

$uploadDir = dirname(__DIR__) . '/public/upload/products';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
    throw new RuntimeException('Impossibile creare la cartella upload/products.');
}

$products = $pdo->query(
    'SELECT p.id, p.name, p.slug, c.slug AS category_slug, pi.id AS image_id, pi.image_path
     FROM products p
     INNER JOIN categories c ON c.id = p.category_id
     INNER JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
     ORDER BY p.id ASC'
)->fetchAll();

$updateImage = $pdo->prepare(
    'UPDATE product_images
     SET image_path = :image_path, alt_text = :alt_text
     WHERE id = :id'
);

$cache = [];
$replaced = 0;
$skipped = 0;

foreach ($products as $product) {
    $currentPath = (string) $product['image_path'];
    $shouldReplace = str_ends_with(strtolower($currentPath), '.png') || $product['slug'] === 'lampada-luna-3d';

    if (!$shouldReplace) {
        $skipped++;
        continue;
    }

    $categorySlug = (string) $product['category_slug'];
    $imageUrl = $categoryImages[$categorySlug] ?? $categoryImages['gioielli-wire'];

    if (!isset($cache[$categorySlug])) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 25,
                'header' => "User-Agent: JHImageRefresh/1.0\r\n",
            ],
        ]);

        $imageData = @file_get_contents($imageUrl, false, $context);
        if ($imageData === false) {
            throw new RuntimeException('Download immagine non riuscito per categoria ' . $categorySlug);
        }

        $cache[$categorySlug] = $imageData;
    }

    $fileName = $product['slug'] . '-real.jpg';
    $absolutePath = $uploadDir . '/' . $fileName;
    $relativePath = 'upload/products/' . $fileName;

    file_put_contents($absolutePath, $cache[$categorySlug]);

    $updateImage->execute([
        'id' => $product['image_id'],
        'image_path' => $relativePath,
        'alt_text' => $product['name'],
    ]);

    $replaced++;
}

echo sprintf("Immagini reali sostituite: %d, lasciate invariate: %d\n", $replaced, $skipped);
