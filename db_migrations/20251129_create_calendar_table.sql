?-- Migration: create calendar_event table
CREATE TABLE IF NOT EXISTS `calendar_event` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `start_datetime` DATETIME NULL,
  `end_datetime` DATETIME NULL,
  `type` VARCHAR(50) DEFAULT 'other',
  `owner_id` INT DEFAULT NULL,
  `participants` TEXT DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
