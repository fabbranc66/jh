CREATE DATABASE IF NOT EXISTS `Sql1874742_3`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `Sql1874742_3`;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`),
  KEY `idx_categories_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(180) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `sku` VARCHAR(80) NULL,
  `short_description` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `materials` TEXT NULL,
  `technique` VARCHAR(150) NULL,
  `price_label` VARCHAR(100) NULL,
  `is_customizable` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `whatsapp_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `telegram_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `share_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `status` VARCHAR(30) NOT NULL DEFAULT 'draft',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  UNIQUE KEY `uq_products_sku` (`sku`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_status_featured` (`status`, `is_featured`),
  CONSTRAINT `fk_products_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `alt_text` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_images_product` (`product_id`),
  KEY `idx_product_images_primary` (`product_id`, `is_primary`),
  CONSTRAINT `fk_product_images_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `attribute_name` VARCHAR(100) NOT NULL,
  `attribute_value` VARCHAR(255) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_attributes_product` (`product_id`),
  CONSTRAINT `fk_product_attributes_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(180) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `content` MEDIUMTEXT NULL,
  `meta_title` VARCHAR(180) NULL,
  `meta_description` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pages_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contact_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(180) NULL,
  `phone` VARCHAR(50) NULL,
  `message` TEXT NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `source` VARCHAR(50) NOT NULL DEFAULT 'website',
  `status` VARCHAR(30) NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_requests_status` (`status`),
  KEY `idx_contact_requests_product` (`product_id`),
  CONSTRAINT `fk_contact_requests_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`)
SELECT * FROM (
  SELECT
    'Gioielli Wire' AS `name`,
    'gioielli-wire' AS `slug`,
    'Ciondoli, orecchini, bracciali e anelli realizzati a mano.' AS `description`,
    10 AS `sort_order`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `categories` WHERE `slug` = 'gioielli-wire'
)
LIMIT 1;

INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`)
SELECT * FROM (
  SELECT
    'Gioielli in Resina' AS `name`,
    'gioielli-resina' AS `slug`,
    'Creazioni in resina artistica con inclusioni e dettagli personalizzati.' AS `description`,
    20 AS `sort_order`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `categories` WHERE `slug` = 'gioielli-resina'
)
LIMIT 1;

INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`)
SELECT * FROM (
  SELECT
    'Stampa 3D' AS `name`,
    'stampa-3d' AS `slug`,
    'Oggetti decorativi, utili e personalizzati realizzati con stampa 3D.' AS `description`,
    30 AS `sort_order`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `categories` WHERE `slug` = 'stampa-3d'
)
LIMIT 1;

INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`)
SELECT * FROM (
  SELECT
    'Eventi' AS `name`,
    'eventi' AS `slug`,
    'Bomboniere, segnaposto, topper e ricordi per occasioni speciali.' AS `description`,
    40 AS `sort_order`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `categories` WHERE `slug` = 'eventi'
)
LIMIT 1;

INSERT INTO `pages` (`title`, `slug`, `content`, `meta_title`, `meta_description`, `is_active`)
SELECT * FROM (
  SELECT
    'Laboratorio' AS `title`,
    'laboratorio' AS `slug`,
    'Pagina introduttiva del laboratorio creativo digitale.' AS `content`,
    'Laboratorio creativo digitale' AS `meta_title`,
    'Artigianato contemporaneo tra manualita e tecnologia.' AS `meta_description`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `pages` WHERE `slug` = 'laboratorio'
)
LIMIT 1;

INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `short_description`, `description`, `materials`, `technique`, `price_label`, `is_customizable`, `is_featured`, `status`)
SELECT * FROM (
  SELECT
    (SELECT id FROM `categories` WHERE `slug` = 'gioielli-wire' LIMIT 1) AS `category_id`,
    'Ciondolo Luna Wire' AS `name`,
    'ciondolo-luna-wire' AS `slug`,
    'LW-023' AS `sku`,
    'Ciondolo artigianale in wire wrapping ispirato alla luna.' AS `short_description`,
    'Pezzo dimostrativo per il catalogo iniziale, utile per testare scheda prodotto, richieste via messaggio e organizzazione dei contenuti.' AS `description`,
    'Filo metallico, pietra decorativa' AS `materials`,
    'Wire wrapping' AS `technique`,
    'Prezzo su richiesta' AS `price_label`,
    1 AS `is_customizable`,
    1 AS `is_featured`,
    'published' AS `status`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `products` WHERE `slug` = 'ciondolo-luna-wire'
)
LIMIT 1;

INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `short_description`, `description`, `materials`, `technique`, `price_label`, `is_customizable`, `is_featured`, `status`)
SELECT * FROM (
  SELECT
    (SELECT id FROM `categories` WHERE `slug` = 'gioielli-resina' LIMIT 1) AS `category_id`,
    'Segnalibro Resina Botanica' AS `name`,
    'segnalibro-resina-botanica' AS `slug`,
    'RB-011' AS `sku`,
    'Segnalibro in resina con inclusioni naturali.' AS `short_description`,
    'Prodotto seed per mostrare una seconda linea creativa del catalogo e testare la navigazione tra categorie.' AS `description`,
    'Resina artistica, inclusioni floreali' AS `materials`,
    'Colata in resina' AS `technique`,
    'Prezzo su richiesta' AS `price_label`,
    1 AS `is_customizable`,
    0 AS `is_featured`,
    'published' AS `status`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `products` WHERE `slug` = 'segnalibro-resina-botanica'
)
LIMIT 1;

INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `short_description`, `description`, `materials`, `technique`, `price_label`, `is_customizable`, `is_featured`, `status`)
SELECT * FROM (
  SELECT
    (SELECT id FROM `categories` WHERE `slug` = 'stampa-3d' LIMIT 1) AS `category_id`,
    'Lampada Luna 3D' AS `name`,
    'lampada-luna-3d' AS `slug`,
    '3DL-004' AS `sku`,
    'Lampada decorativa stampata in 3D con look contemporaneo.' AS `short_description`,
    'Seed iniziale per la linea stampa 3D, utile per mostrare prodotti decorativi e futuri oggetti smart.' AS `description`,
    'PLA, modulo luce LED' AS `materials`,
    'Stampa 3D' AS `technique`,
    'Prezzo su richiesta' AS `price_label`,
    1 AS `is_customizable`,
    1 AS `is_featured`,
    'published' AS `status`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `products` WHERE `slug` = 'lampada-luna-3d'
)
LIMIT 1;
