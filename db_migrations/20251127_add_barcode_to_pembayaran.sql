-- Migration: add barcode column to pembayaran
-- Run this in your MySQL console or via phpMyAdmin

ALTER TABLE pembayaran
  ADD COLUMN barcode VARCHAR(255) NULL AFTER bukti,
  ADD UNIQUE KEY unique_barcode (barcode);

-- Optional: if you prefer to store generated barcode images, add:
-- ALTER TABLE pembayaran ADD COLUMN barcode_image VARCHAR(255) NULL AFTER barcode;
