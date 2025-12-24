-- Migration: create private_chat table for admin-operator private messages
-- Run this SQL once (e.g., using phpMyAdmin or mysql CLI)

CREATE TABLE IF NOT EXISTS `private_chat` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` INT UNSIGNED NOT NULL,
  `recipient_id` INT UNSIGNED NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`sender_id`),
  INDEX (`recipient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
