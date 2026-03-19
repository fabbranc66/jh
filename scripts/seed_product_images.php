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

$uploadDir = dirname(__DIR__) . '/public/upload/products';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
    throw new RuntimeException('Impossibile creare la cartella upload/products.');
}

$palette = [
    'gioielli-wire' => ['1f4f95', 'ffffff'],
    'gioielli-resina' => ['7a5ac8', 'ffffff'],
    'gioielli-ibridi' => ['4f6bd9', 'ffffff'],
    'oggettistica-resina' => ['2e7d73', 'ffffff'],
    'stampa-3d' => ['375a7f', 'ffffff'],
    'smart-objects' => ['0f766e', 'ffffff'],
    'segnaletica-personalizzata' => ['8b5e34', 'ffffff'],
    'eventi' => ['b85c7a', 'ffffff'],
    'pet' => ['7b4f2f', 'ffffff'],
    'scanner-3d' => ['475569', 'ffffff'],
    'reverse-engineering' => ['334155', 'ffffff'],
    'accessori-vape' => ['5b4b8a', 'ffffff'],
];

$products = $pdo->query(
    'SELECT p.id, p.name, p.slug, c.slug AS category_slug
     FROM products p
     INNER JOIN categories c ON c.id = p.category_id
     ORDER BY p.id ASC'
)->fetchAll();

$findImages = $pdo->prepare('SELECT COUNT(*) FROM product_images WHERE product_id = :product_id');
$insertImage = $pdo->prepare(
    'INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary)
     VALUES (:product_id, :image_path, :alt_text, 0, 1)'
);

$imported = 0;
$skipped = 0;

foreach ($products as $product) {
    $findImages->execute(['product_id' => $product['id']]);
    if ((int) $findImages->fetchColumn() > 0) {
        $skipped++;
        continue;
    }

    [$background, $foreground] = $palette[$product['category_slug']] ?? ['1f4f95', 'ffffff'];
    $fileName = $product['slug'] . '.png';
    $relativePath = 'upload/products/' . $fileName;
    $targetPath = $uploadDir . '/' . $fileName;

    if (!is_file($targetPath)) {
        $label = rawurlencode($product['name']);
        $url = sprintf(
            'https://dummyimage.com/1200x1200/%s/%s.png&text=%s',
            $background,
            $foreground,
            $label
        );

        $context = stream_context_create([
            'http' => [
                'timeout' => 20,
                'header' => "User-Agent: JHSeeder/1.0\r\n",
            ],
        ]);

        $imageData = @file_get_contents($url, false, $context);
        if ($imageData === false) {
            throw new RuntimeException('Download immagine non riuscito per ' . $product['slug']);
        }

        file_put_contents($targetPath, $imageData);
    }

    $insertImage->execute([
        'product_id' => $product['id'],
        'image_path' => $relativePath,
        'alt_text' => $product['name'],
    ]);

    $imported++;
}

echo sprintf("Immagini associate: %d, gia presenti: %d\n", $imported, $skipped);
