-- =====================================================
-- WAREHOUSE MANAGEMENT SYSTEM (WMS) - ERD SQL
-- Entity Relationship Diagram in SQL Format
-- Generated: 2026-01-02
-- =====================================================

-- Drop existing tables (if any) to start fresh
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `stock_movements`;
DROP TABLE IF EXISTS `stocks`;
DROP TABLE IF EXISTS `goods_ins`;
DROP TABLE IF EXISTS `batches`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `locations`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `jobs`;
SET FOREIGN_KEY_CHECKS=1;

-- =====================================================
-- CORE TABLES
-- =====================================================

-- 1. USERS TABLE (Authentication & Authorization)
-- └─ Stores user account information for system access
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_email (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. PRODUCTS TABLE
-- └─ Master data for all products in inventory
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `sku` VARCHAR(255) NOT NULL UNIQUE,
  `barcode` VARCHAR(255) NULL DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `unit` VARCHAR(50) NOT NULL COMMENT 'e.g., Pcs, Box, Kg, etc.',
  `category` VARCHAR(100) NULL DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'active' COMMENT 'active, inactive, discontinued',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY uk_sku (`sku`),
  INDEX idx_status (`status`),
  INDEX idx_category (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. LOCATIONS TABLE
-- └─ Physical storage locations in the warehouse
-- └─ Structure: Rack > Slot (example: Rack A, Slot 1)
CREATE TABLE `locations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(100) NOT NULL UNIQUE,
  `rack` VARCHAR(50) NOT NULL COMMENT 'Rack identifier (e.g., A, B, C)',
  `slot` VARCHAR(50) NOT NULL COMMENT 'Slot identifier (e.g., 1, 2, 3)',
  `capacity` INT NULL DEFAULT NULL COMMENT 'Maximum quantity this location can hold',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY uk_location (`rack`, `slot`),
  INDEX idx_code (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. SUPPLIERS TABLE
-- └─ Vendor/supplier information for incoming goods
CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(20) NULL DEFAULT NULL,
  `address` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_email (`email`),
  INDEX idx_name (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. CUSTOMERS TABLE
-- └─ Customer information for outgoing goods
CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(20) NULL DEFAULT NULL,
  `address` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_email (`email`),
  INDEX idx_name (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INVENTORY MANAGEMENT TABLES
-- =====================================================

-- 6. BATCHES TABLE
-- └─ Product batches with manufacture and expiry dates
-- └─ Relationship: Product -> Batches (1 Product can have many Batches)
CREATE TABLE `batches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `batch_code` VARCHAR(100) NOT NULL UNIQUE,
  `manufacture_date` DATE NOT NULL,
  `expiry_date` DATE NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY fk_batches_product (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_product_id (`product_id`),
  INDEX idx_batch_code (`batch_code`),
  INDEX idx_expiry_date (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. STOCKS TABLE
-- └─ Current inventory position for each product-batch-location combination
-- └─ Relationships:
--    - Product -> Stocks (1 Product can have many Stock records)
--    - Batch -> Stocks (1 Batch can have many Stock records)
--    - Location -> Stocks (1 Location can hold many Products/Batches)
CREATE TABLE `stocks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `batch_id` BIGINT UNSIGNED NOT NULL,
  `location_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY fk_stocks_product (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY fk_stocks_batch (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY fk_stocks_location (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY uk_stock_position (`product_id`, `batch_id`, `location_id`),
  INDEX idx_product_id (`product_id`),
  INDEX idx_batch_id (`batch_id`),
  INDEX idx_location_id (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TRANSACTION & MOVEMENT TABLES
-- =====================================================

-- 8. STOCK_MOVEMENTS TABLE
-- └─ Audit trail of all inventory movements (IN/OUT)
-- └─ Used for tracking stock changes and reconciliation
-- └─ Relationships:
--    - Product -> Stock Movements
--    - Batch -> Stock Movements
--    - Location -> Stock Movements
CREATE TABLE `stock_movements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `date` DATE NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `batch_id` BIGINT UNSIGNED NOT NULL,
  `location_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('IN', 'OUT') NOT NULL COMMENT 'IN: Goods In, OUT: Goods Out',
  `quantity` INT NOT NULL,
  `reference` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Reference to Goods In/Out ID',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY fk_movements_product (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY fk_movements_batch (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY fk_movements_location (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_date (`date`),
  INDEX idx_type (`type`),
  INDEX idx_product_id (`product_id`),
  INDEX idx_reference (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. GOODS_INS TABLE
-- └─ Incoming goods from suppliers
-- └─ Relationships:
--    - Supplier -> Goods Ins (1 Supplier can supply many Goods In)
--    - Product -> Goods Ins (1 Product can have many Goods In transactions)
--    - Batch -> Goods Ins (1 Batch can have 1+ Goods In records)
--    - Location -> Goods Ins (Goods In are stored in Locations)
CREATE TABLE `goods_ins` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `reference_number` VARCHAR(100) NOT NULL UNIQUE COMMENT 'PO number or Invoice number',
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `batch_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'Nullable if batch is to be created',
  `batch_code` VARCHAR(100) NOT NULL,
  `manufacture_date` DATE NOT NULL,
  `expiry_date` DATE NOT NULL,
  `quantity` INT NOT NULL,
  `location_id` BIGINT UNSIGNED NOT NULL,
  `notes` TEXT NULL DEFAULT NULL,
  `status` ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY fk_goods_ins_supplier (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY fk_goods_ins_product (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY fk_goods_ins_batch (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY fk_goods_ins_location (`location_id`) REFERENCES `locations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_reference_number (`reference_number`),
  INDEX idx_supplier_id (`supplier_id`),
  INDEX idx_product_id (`product_id`),
  INDEX idx_status (`status`),
  INDEX idx_expiry_date (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- FRAMEWORK/UTILITY TABLES (Laravel specific)
-- =====================================================

-- 10. CACHE TABLE
-- └─ Laravel cache storage
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL PRIMARY KEY,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  INDEX idx_expiration (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. JOBS TABLE
-- └─ Laravel queue jobs
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `reserved_at` INT UNSIGNED NULL DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  INDEX idx_queue (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. PASSWORD_RESET_TOKENS TABLE
-- └─ Password reset token storage
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL PRIMARY KEY,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. SESSIONS TABLE
-- └─ User session storage
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  FOREIGN KEY fk_sessions_user (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_user_id (`user_id`),
  INDEX idx_last_activity (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RELATIONSHIP SUMMARY / ERD DESCRIPTION
-- =====================================================
/*

   ENTITY RELATIONSHIP DIAGRAM (ERD) - WMS (Warehouse Management System)

   ┌─────────────────────────────────────────────────────────────────────┐
   │                    CORE MASTER DATA                                  │
   ├─────────────────────────────────────────────────────────────────────┤
   │                                                                      │
   │  USERS (1) ──────────────── SESSIONS (N)                           │
   │   - id (PK)              - id (PK)                                  │
   │   - name                 - user_id (FK)                             │
   │   - email                - ip_address                               │
   │   - password             - payload                                  │
   │                                                                      │
   │  PRODUCTS (1) ───────────── BATCHES (N)                            │
   │   - id (PK)              - id (PK)                                  │
   │   - sku                  - product_id (FK)                          │
   │   - barcode              - batch_code                               │
   │   - name                 - manufacture_date                         │
   │   - unit                 - expiry_date                              │
   │   - category             - quantity                                 │
   │                                                                      │
   │  LOCATIONS (1)           SUPPLIERS (1)      CUSTOMERS (1)          │
   │   - id (PK)              - id (PK)          - id (PK)              │
   │   - code                 - name             - name                 │
   │   - rack                 - email            - email                │
   │   - slot                 - phone            - phone                │
   │   - capacity             - address          - address              │
   │                                                                      │
   └─────────────────────────────────────────────────────────────────────┘

   ┌─────────────────────────────────────────────────────────────────────┐
   │                  INVENTORY MANAGEMENT                                │
   ├─────────────────────────────────────────────────────────────────────┤
   │                                                                      │
   │  STOCKS (Current Inventory)                                         │
   │   - id (PK)                                                         │
   │   - product_id (FK) → PRODUCTS                                     │
   │   - batch_id (FK) → BATCHES                                        │
   │   - location_id (FK) → LOCATIONS                                   │
   │   - quantity                                                        │
   │   - Unique: (product_id, batch_id, location_id)                   │
   │                                                                      │
   │   (Represents: Where is each product batch stored and how much)    │
   │                                                                      │
   └─────────────────────────────────────────────────────────────────────┘

   ┌─────────────────────────────────────────────────────────────────────┐
   │                 TRANSACTION MANAGEMENT                               │
   ├─────────────────────────────────────────────────────────────────────┤
   │                                                                      │
   │  GOODS_INS (Incoming Goods from Supplier)                          │
   │   - id (PK)                                                         │
   │   - reference_number (Unique)                                       │
   │   - supplier_id (FK) → SUPPLIERS                                   │
   │   - product_id (FK) → PRODUCTS                                     │
   │   - batch_id (FK) → BATCHES                                        │
   │   - location_id (FK) → LOCATIONS                                   │
   │   - batch_code, manufacture_date, expiry_date                     │
   │   - quantity, notes, status                                         │
   │                                                                      │
   │   (Represents: Every goods in transaction from suppliers)          │
   │                                                                      │
   │  STOCK_MOVEMENTS (Audit Trail of Inventory Changes)                │
   │   - id (PK)                                                         │
   │   - date                                                            │
   │   - product_id (FK) → PRODUCTS                                     │
   │   - batch_id (FK) → BATCHES                                        │
   │   - location_id (FK) → LOCATIONS                                   │
   │   - type (ENUM: IN, OUT)                                           │
   │   - quantity                                                        │
   │   - reference (Links to GOODS_INS/GOODS_OUT)                       │
   │                                                                      │
   │   (Represents: Complete history of stock movements for audit)      │
   │                                                                      │
   └─────────────────────────────────────────────────────────────────────┘

   KEY RELATIONSHIPS:
   ==================

   1. Products → Batches (1:N)
      A product can have multiple batches (different manufacture/expiry dates)

   2. Products → Stocks (1:N)
      A product can be stocked at multiple locations

   3. Batches → Stocks (1:N)
      A batch can be stored at multiple locations

   4. Locations → Stocks (1:N)
      A location can hold multiple products/batches

   5. Products → Stock_Movements (1:N)
      All stock movements are tracked per product

   6. Batches → Stock_Movements (1:N)
      All stock movements are tracked per batch

   7. Suppliers → Goods_Ins (1:N)
      A supplier can have multiple goods in transactions

   8. Products → Goods_Ins (1:N)
      A product can have multiple goods in transactions

   9. Users → Sessions (1:N)
      A user can have multiple active sessions

   BUSINESS LOGIC:
   ===============

   - When GOODS_IN is completed:
     1. Create/Update BATCH (if not exists)
     2. Create/Update STOCK record (product+batch+location)
     3. Create STOCK_MOVEMENT record (type='IN')

   - When GOODS_OUT happens (future):
     1. Decrease STOCK quantity
     2. Create STOCK_MOVEMENT record (type='OUT')

   - STOCK table is the real-time inventory snapshot
   - STOCK_MOVEMENTS table is the audit trail/history

*/

-- =====================================================
-- SAMPLE DATA (Optional - Comment out if not needed)
-- =====================================================

-- Sample Users
INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `updated_at`) VALUES
('Admin User', 'admin@wms.local', '$2y$12$ZQvz0LjQ6c0kJ3X8L2m9O.Hj0h5d3c2b1a0Z9y8x7w6v5u4t3s2r1q', NOW(), NOW()),
('Warehouse Staff', 'staff@wms.local', '$2y$12$ZQvz0LjQ6c0kJ3X8L2m9O.Hj0h5d3c2b1a0Z9y8x7w6v5u4t3s2r1q', NOW(), NOW());

-- Sample Products
INSERT INTO `products` (`sku`, `barcode`, `name`, `unit`, `category`, `status`, `created_at`, `updated_at`) VALUES
('SKU-001', '1234567890001', 'Product A - Electronics', 'Pcs', 'Electronics', 'active', NOW(), NOW()),
('SKU-002', '1234567890002', 'Product B - Textiles', 'Box', 'Textiles', 'active', NOW(), NOW()),
('SKU-003', '1234567890003', 'Product C - Food', 'Kg', 'Food', 'active', NOW(), NOW());

-- Sample Locations
INSERT INTO `locations` (`code`, `rack`, `slot`, `capacity`, `created_at`, `updated_at`) VALUES
('LOC-A1', 'A', '1', 100, NOW(), NOW()),
('LOC-A2', 'A', '2', 100, NOW(), NOW()),
('LOC-B1', 'B', '1', 150, NOW(), NOW());

-- Sample Suppliers
INSERT INTO `suppliers` (`name`, `email`, `phone`, `address`, `created_at`, `updated_at`) VALUES
('PT. Supplier Indonesia', 'contact@supplier1.id', '021-12345678', 'Jakarta', NOW(), NOW()),
('PT. Distributor Jaya', 'info@supplier2.id', '031-87654321', 'Surabaya', NOW(), NOW());

-- Sample Customers
INSERT INTO `customers` (`name`, `email`, `phone`, `address`, `created_at`, `updated_at`) VALUES
('PT. Retail ABC', 'order@retail1.id', '021-11223344', 'Jakarta', NOW(), NOW()),
('PT. Toko Sejahtera', 'contact@retail2.id', '031-55667788', 'Bandung', NOW(), NOW());

-- Sample Batches
INSERT INTO `batches` (`product_id`, `batch_code`, `manufacture_date`, `expiry_date`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 'BATCH-001', '2025-12-15', '2026-12-15', 100, NOW(), NOW()),
(1, 'BATCH-002', '2025-12-20', '2026-12-20', 100, NOW(), NOW()),
(2, 'BATCH-003', '2025-11-01', '2027-11-01', 200, NOW(), NOW()),
(3, 'BATCH-004', '2026-01-01', '2026-07-01', 500, NOW(), NOW());

-- Sample Stocks (Current Inventory)
INSERT INTO `stocks` (`product_id`, `batch_id`, `location_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 50, NOW(), NOW()),
(1, 2, 1, 40, NOW(), NOW()),
(1, 2, 2, 60, NOW(), NOW()),
(2, 3, 2, 100, NOW(), NOW()),
(2, 3, 3, 100, NOW(), NOW()),
(3, 4, 3, 250, NOW(), NOW());

-- Sample Stock Movements
INSERT INTO `stock_movements` (`date`, `product_id`, `batch_id`, `location_id`, `type`, `quantity`, `reference`, `created_at`, `updated_at`) VALUES
('2026-01-01', 1, 1, 1, 'IN', 50, 'GI-001', NOW(), NOW()),
('2026-01-01', 1, 2, 1, 'IN', 40, 'GI-002', NOW(), NOW()),
('2026-01-01', 1, 2, 2, 'IN', 60, 'GI-003', NOW(), NOW()),
('2026-01-01', 2, 3, 2, 'IN', 100, 'GI-004', NOW(), NOW()),
('2026-01-01', 2, 3, 3, 'IN', 100, 'GI-005', NOW(), NOW()),
('2026-01-02', 3, 4, 3, 'IN', 250, 'GI-006', NOW(), NOW());

-- Sample Goods In
INSERT INTO `goods_ins` (`reference_number`, `supplier_id`, `product_id`, `batch_id`, `batch_code`, `manufacture_date`, `expiry_date`, `quantity`, `location_id`, `notes`, `status`, `created_at`, `updated_at`) VALUES
('GI-001', 1, 1, 1, 'BATCH-001', '2025-12-15', '2026-12-15', 50, 1, 'Initial stock', 'completed', NOW(), NOW()),
('GI-002', 1, 1, 2, 'BATCH-002', '2025-12-20', '2026-12-20', 40, 1, 'Second batch', 'completed', NOW(), NOW()),
('GI-003', 1, 1, 2, 'BATCH-002', '2025-12-20', '2026-12-20', 60, 2, 'Third batch in different location', 'completed', NOW(), NOW()),
('GI-004', 2, 2, 3, 'BATCH-003', '2025-11-01', '2027-11-01', 100, 2, 'Textiles stock', 'completed', NOW(), NOW()),
('GI-005', 2, 2, 3, 'BATCH-003', '2025-11-01', '2027-11-01', 100, 3, 'Textiles stock 2', 'completed', NOW(), NOW()),
('GI-006', 1, 3, 4, 'BATCH-004', '2026-01-01', '2026-07-01', 250, 3, 'Food items', 'completed', NOW(), NOW());

-- =====================================================
-- END OF ERD SQL FILE
-- =====================================================
