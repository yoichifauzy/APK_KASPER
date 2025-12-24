-- Migration: add color columns to calendar_event
ALTER TABLE `calendar_event`
  ADD COLUMN `bg_color` VARCHAR(16) DEFAULT NULL,
  ADD COLUMN `text_color` VARCHAR(16) DEFAULT NULL;
