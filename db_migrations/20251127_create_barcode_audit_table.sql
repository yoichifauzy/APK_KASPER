-- Migration: create barcode_audit table
-- Run this using your preferred DB tool: mysql -u user -p database < thisfile.sql
CREATE TABLE IF NOT EXISTS `barcode_audit` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `barcode` VARCHAR(255) NOT NULL,
  `id_user` INT NULL,
  `action` VARCHAR(50) NOT NULL,
  `ip` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `extra` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`barcode`),
  INDEX (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
