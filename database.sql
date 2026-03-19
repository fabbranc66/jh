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
  `ip_address` VARCHAR(45) NULL,
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

CREATE TABLE IF NOT EXISTS `home_slides` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(180) NOT NULL,
  `subtitle` VARCHAR(255) NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `link_url` VARCHAR(255) NOT NULL,
  `button_label` VARCHAR(60) NOT NULL DEFAULT 'Scopri',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_home_slides_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_site_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `components` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(80) NOT NULL,
  `name` VARCHAR(180) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `category` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `unit` VARCHAR(20) NOT NULL DEFAULT 'pz',
  `current_stock` DECIMAL(12,3) NOT NULL DEFAULT 0,
  `reorder_level` DECIMAL(12,3) NOT NULL DEFAULT 0,
  `pack_quantity` DECIMAL(12,3) NULL,
  `last_price` DECIMAL(10,2) NULL,
  `supplier_name` VARCHAR(150) NULL,
  `supplier_sku` VARCHAR(100) NULL,
  `purchase_url` VARCHAR(255) NULL,
  `location` VARCHAR(120) NULL,
  `lead_time_days` INT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_components_code` (`code`),
  UNIQUE KEY `uq_components_slug` (`slug`),
  KEY `idx_components_active` (`is_active`),
  KEY `idx_components_reorder` (`is_active`, `reorder_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_bom_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `component_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(12,3) NOT NULL DEFAULT 1,
  `unit` VARCHAR(20) NOT NULL DEFAULT 'pz',
  `waste_percent` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `notes` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_product_component` (`product_id`, `component_id`),
  KEY `idx_bom_product` (`product_id`),
  KEY `idx_bom_component` (`component_id`),
  CONSTRAINT `fk_bom_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_bom_component`
    FOREIGN KEY (`component_id`) REFERENCES `components` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `products`
  ADD COLUMN IF NOT EXISTS `product_type` VARCHAR(30) NOT NULL DEFAULT 'finished' AFTER `sku`,
  ADD COLUMN IF NOT EXISTS `production_time_hours` DECIMAL(8,2) NULL AFTER `technique`,
  ADD COLUMN IF NOT EXISTS `internal_cost` DECIMAL(10,2) NULL AFTER `production_time_hours`,
  ADD COLUMN IF NOT EXISTS `minimum_stock` DECIMAL(12,3) NULL AFTER `internal_cost`,
  ADD COLUMN IF NOT EXISTS `internal_notes` TEXT NULL AFTER `minimum_stock`;

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

INSERT INTO `home_slides` (`title`, `subtitle`, `image_url`, `link_url`, `button_label`, `sort_order`, `is_active`)
SELECT * FROM (
  SELECT
    'Gioielli da regalare' AS `title`,
    'Linee curate, tono premium e schede chiare per trovare subito l idea giusta.' AS `subtitle`,
    'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=1600&q=80' AS `image_url`,
    '/categoria/gioielli-wire' AS `link_url`,
    'Vai ai gioielli' AS `button_label`,
    10 AS `sort_order`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `home_slides` WHERE `sort_order` = 10
)
LIMIT 1;

INSERT INTO `site_settings` (`setting_key`, `setting_value`)
SELECT * FROM (
  SELECT
    'site_logo_path' AS `setting_key`,
    'assets/images/logo_jh.png' AS `setting_value`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `site_settings` WHERE `setting_key` = 'site_logo_path'
)
LIMIT 1;

INSERT INTO `home_slides` (`title`, `subtitle`, `image_url`, `link_url`, `button_label`, `sort_order`, `is_active`)
SELECT * FROM (
  SELECT
    'Regali e ricordi personalizzati' AS `title`,
    'Piccoli oggetti da scegliere per occasione, tema o significato.' AS `subtitle`,
    'https://images.unsplash.com/photo-1617038220319-276d3cfab638?auto=format&fit=crop&w=1600&q=80' AS `image_url`,
    '/categoria/eventi' AS `link_url`,
    'Scopri eventi' AS `button_label`,
    20 AS `sort_order`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `home_slides` WHERE `sort_order` = 20
)
LIMIT 1;

INSERT INTO `home_slides` (`title`, `subtitle`, `image_url`, `link_url`, `button_label`, `sort_order`, `is_active`)
SELECT * FROM (
  SELECT
    'Decorazioni e stampa 3D' AS `title`,
    'Oggetti creativi tra artigianato, casa e soluzioni piu contemporanee.' AS `subtitle`,
    'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?auto=format&fit=crop&w=1600&q=80' AS `image_url`,
    '/categoria/stampa-3d' AS `link_url`,
    'Apri la collezione' AS `button_label`,
    30 AS `sort_order`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `home_slides` WHERE `sort_order` = 30
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

INSERT INTO `components` (`code`, `name`, `slug`, `category`, `description`, `unit`, `current_stock`, `reorder_level`, `supplier_name`, `purchase_url`, `is_active`)
SELECT * FROM (
  SELECT
    'WIRE-RAME-08' AS `code`,
    'Filo rame 0.8 mm' AS `name`,
    'filo-rame-0-8-mm' AS `slug`,
    'Wire' AS `category`,
    'Filo per lavorazioni wire wrapping.' AS `description`,
    'm' AS `unit`,
    24.000 AS `current_stock`,
    5.000 AS `reorder_level`,
    'Fornitore wire' AS `supplier_name`,
    'https://example.com/wire-rame' AS `purchase_url`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `components` WHERE `code` = 'WIRE-RAME-08'
)
LIMIT 1;

INSERT INTO `components` (`code`, `name`, `slug`, `category`, `description`, `unit`, `current_stock`, `reorder_level`, `supplier_name`, `purchase_url`, `is_active`)
SELECT * FROM (
  SELECT
    'RES-TRASP-1KG' AS `code`,
    'Resina trasparente 1 kg' AS `name`,
    'resina-trasparente-1-kg' AS `slug`,
    'Resina' AS `category`,
    'Base trasparente per colate artistiche.' AS `description`,
    'kg' AS `unit`,
    1.500 AS `current_stock`,
    1.000 AS `reorder_level`,
    'Fornitore resina' AS `supplier_name`,
    'https://example.com/resina-trasparente' AS `purchase_url`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `components` WHERE `code` = 'RES-TRASP-1KG'
)
LIMIT 1;

INSERT INTO `components` (`code`, `name`, `slug`, `category`, `description`, `unit`, `current_stock`, `reorder_level`, `supplier_name`, `purchase_url`, `is_active`)
SELECT * FROM (
  SELECT
    'PLA-BIANCO-1KG' AS `code`,
    'PLA bianco 1 kg' AS `name`,
    'pla-bianco-1-kg' AS `slug`,
    'Stampa 3D' AS `category`,
    'Filamento PLA per stampa 3D.' AS `description`,
    'kg' AS `unit`,
    0.700 AS `current_stock`,
    1.000 AS `reorder_level`,
    'Fornitore stampa 3D' AS `supplier_name`,
    'https://example.com/pla-bianco' AS `purchase_url`,
    1 AS `is_active`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `components` WHERE `code` = 'PLA-BIANCO-1KG'
)
LIMIT 1;

INSERT INTO `product_bom_items` (`product_id`, `component_id`, `quantity`, `unit`, `waste_percent`, `notes`, `sort_order`)
SELECT * FROM (
  SELECT
    (SELECT id FROM `products` WHERE `slug` = 'ciondolo-luna-wire' LIMIT 1) AS `product_id`,
    (SELECT id FROM `components` WHERE `code` = 'WIRE-RAME-08' LIMIT 1) AS `component_id`,
    2.000 AS `quantity`,
    'm' AS `unit`,
    5.00 AS `waste_percent`,
    'Consumo medio per un ciondolo standard.' AS `notes`,
    10 AS `sort_order`
) AS tmp
WHERE NOT EXISTS (
  SELECT 1
  FROM `product_bom_items`
  WHERE `product_id` = (SELECT id FROM `products` WHERE `slug` = 'ciondolo-luna-wire' LIMIT 1)
    AND `component_id` = (SELECT id FROM `components` WHERE `code` = 'WIRE-RAME-08' LIMIT 1)
)
LIMIT 1;
