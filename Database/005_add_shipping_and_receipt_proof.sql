-- 005: Add shipping receipt and receive proof columns to requests table
ALTER TABLE `requests` 
ADD COLUMN `shipping_receipt` VARCHAR(255) NULL AFTER `shipped_at`,
ADD COLUMN `received_proof` VARCHAR(255) NULL AFTER `shipping_receipt`,
ADD COLUMN `received_at` DATETIME NULL AFTER `received_proof`,
ADD COLUMN `received_notes` TEXT NULL AFTER `received_at`;
