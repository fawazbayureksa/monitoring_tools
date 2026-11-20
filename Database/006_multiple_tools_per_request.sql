-- 006: Multiple Tools per Request (Header-Detail Structure)
-- Author: Antigravity
-- Date: 2026-11-19
-- Description:
--   Creates request_items table to store multiple tools per request,
--   adds unique constraint per request-tool pair, foreign key constraints,
--   and tracks stock restoration idempotency via stock_restored flag.

-- 1. Create request_items table
CREATE TABLE IF NOT EXISTS `request_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` INT(11) NOT NULL,
  `tool_id` INT(11) NOT NULL,
  `qty_requested` INT(11) NOT NULL DEFAULT 1,
  `qty_returned` INT(11) NOT NULL DEFAULT 0,
  `return_condition` ENUM('good', 'broken', 'lost') DEFAULT NULL,
  `stock_restored` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if stock already restored to tools table, prevents duplicate restoration',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_request_tool` (`request_id`, `tool_id`),
  KEY `idx_request_id` (`request_id`),
  KEY `idx_tool_id` (`tool_id`),
  CONSTRAINT `fk_request_items_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_request_items_tool` FOREIGN KEY (`tool_id`) REFERENCES `tools` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Migrate existing historical data from requests into request_items
-- Idempotent: only inserts records where request_id does not already exist in request_items
INSERT INTO `request_items` (`request_id`, `tool_id`, `qty_requested`, `qty_returned`, `return_condition`, `stock_restored`, `created_at`)
SELECT 
    r.id AS request_id,
    r.tool_id,
    r.qty_requested,
    CASE WHEN r.status = 'Completed' THEN r.qty_requested ELSE 0 END AS qty_returned,
    CASE 
        WHEN r.status = 'Completed' THEN 'good'
        WHEN r.status LIKE '%RUSAK%' THEN 'broken'
        WHEN r.status LIKE '%HILANG%' THEN 'lost'
        ELSE NULL 
    END AS return_condition,
    CASE 
        WHEN r.status IN ('Completed', 'Declined', 'Cancelled') THEN 1 
        ELSE 0 
    END AS stock_restored,
    r.created_at
FROM `requests` r
WHERE r.tool_id IS NOT NULL 
  AND r.tool_id > 0
  AND NOT EXISTS (
      SELECT 1 FROM `request_items` ri WHERE ri.request_id = r.id
  );

-- =========================================================
-- Rollback Instructions (Safe migration rollback)
-- =========================================================
-- To rollback:
-- ALTER TABLE `request_items` DROP FOREIGN KEY `fk_request_items_request`;
-- ALTER TABLE `request_items` DROP FOREIGN KEY `fk_request_items_tool`;
-- DROP TABLE IF EXISTS `request_items`;
