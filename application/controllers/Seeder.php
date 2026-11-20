<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seeder extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function spv() {
        // Pastikan kolom role pada tabel users mendukung ENUM 'supervisor'
        $this->db->query("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('master','admin','leader','user','supervisor') DEFAULT 'user'");

        $spv_users = [
            [
                'nrp'      => 'spv',
                'username' => 'spv',
                'name'     => 'Supervisor',
                'role'     => 'supervisor',
                'password' => 'spv123'
            ],
            [
                'nrp'      => 'spv_field',
                'username' => 'spv_field',
                'name'     => 'Supervisor Field',
                'role'     => 'supervisor',
                'password' => 'spv123'
            ]
        ];

        $is_cli = is_cli();

        foreach ($spv_users as $user) {
            $existing = $this->db->get_where('users', ['nrp' => $user['nrp']])->row_array();
            if ($existing) {
                $this->db->where('id', $existing['id']);
                $this->db->update('users', $user);
                $msg = "[UPDATED] User '{$user['nrp']}' (Role: {$user['role']}) berhasil diperbarui.";
            } else {
                $this->db->insert('users', $user);
                $msg = "[CREATED] User '{$user['nrp']}' (Role: {$user['role']}) berhasil dibuat.";
            }
            echo $is_cli ? $msg . PHP_EOL : "<p style='color:green;'>$msg</p>";
        }

        $done_msg = "Seeder SPV selesai dijalankan.";
        echo $is_cli ? $done_msg . PHP_EOL : "<p><strong>$done_msg</strong></p>";
    }

    public function migration_005() {
        $fields = $this->db->list_fields('requests');
        $is_cli = is_cli();

        if (!in_array('shipping_receipt', $fields)) {
            $this->db->query("ALTER TABLE `requests` ADD COLUMN `shipping_receipt` VARCHAR(255) NULL AFTER `shipped_at`");
            $msg = "[ADDED] Kolom 'shipping_receipt' berhasil ditambahkan ke tabel requests.";
            echo $is_cli ? $msg . PHP_EOL : "<p style='color:green;'>$msg</p>";
        }
        if (!in_array('received_proof', $fields)) {
            $this->db->query("ALTER TABLE `requests` ADD COLUMN `received_proof` VARCHAR(255) NULL AFTER `shipping_receipt`");
            $msg = "[ADDED] Kolom 'received_proof' berhasil ditambahkan ke tabel requests.";
            echo $is_cli ? $msg . PHP_EOL : "<p style='color:green;'>$msg</p>";
        }
        if (!in_array('received_at', $fields)) {
            $this->db->query("ALTER TABLE `requests` ADD COLUMN `received_at` DATETIME NULL AFTER `received_proof`");
            $msg = "[ADDED] Kolom 'received_at' berhasil ditambahkan ke tabel requests.";
            echo $is_cli ? $msg . PHP_EOL : "<p style='color:green;'>$msg</p>";
        }
        if (!in_array('received_notes', $fields)) {
            $this->db->query("ALTER TABLE `requests` ADD COLUMN `received_notes` TEXT NULL AFTER `received_at`");
            $msg = "[ADDED] Kolom 'received_notes' berhasil ditambahkan ke tabel requests.";
            echo $is_cli ? $msg . PHP_EOL : "<p style='color:green;'>$msg</p>";
        }

        $done_msg = "Migration 005 selesai dijalankan.";
        echo $is_cli ? $done_msg . PHP_EOL : "<p><strong>$done_msg</strong></p>";
    }

    public function migration_006() {
        $is_cli = is_cli();

        // 1. Buat tabel request_items jika belum ada
        if (!$this->db->table_exists('request_items')) {
            $sql = "CREATE TABLE IF NOT EXISTS `request_items` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `request_id` INT(11) NOT NULL,
                `tool_id` INT(11) NOT NULL,
                `qty_requested` INT(11) NOT NULL DEFAULT 1,
                `qty_returned` INT(11) NOT NULL DEFAULT 0,
                `return_condition` ENUM('good', 'broken', 'lost') DEFAULT NULL,
                `stock_restored` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_request_tool` (`request_id`, `tool_id`),
                KEY `idx_request_id` (`request_id`),
                KEY `idx_tool_id` (`tool_id`),
                CONSTRAINT `fk_request_items_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_request_items_tool` FOREIGN KEY (`tool_id`) REFERENCES `tools` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $this->db->query($sql);
            $msg = "[CREATED] Tabel 'request_items' berhasil dibuat.";
            echo $is_cli ? $msg . PHP_EOL : "<p style='color:green;'>$msg</p>";
        } else {
            // Pastikan kolom stock_restored ada
            if (!$this->db->field_exists('stock_restored', 'request_items')) {
                $this->db->query("ALTER TABLE `request_items` ADD COLUMN `stock_restored` TINYINT(1) NOT NULL DEFAULT 0 AFTER `return_condition`");
            }
            $msg = "[EXISTS] Tabel 'request_items' sudah ada.";
            echo $is_cli ? $msg . PHP_EOL : "<p style='color:blue;'>$msg</p>";
        }

        // 2. Migrasikan data request lama ke request_items jika belum termigrasi
        $migrate_sql = "INSERT INTO `request_items` (`request_id`, `tool_id`, `qty_requested`, `qty_returned`, `return_condition`, `stock_restored`, `created_at`)
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
              );";
        $this->db->query($migrate_sql);
        $affected = $this->db->affected_rows();
        $msg = "[MIGRATED] {$affected} data permintaan lama berhasil dimigrasikan ke 'request_items'.";
        echo $is_cli ? $msg . PHP_EOL : "<p style='color:green;'>$msg</p>";

        $done_msg = "Migration 006 (Multiple Tools per Request) selesai dijalankan.";
        echo $is_cli ? $done_msg . PHP_EOL : "<p><strong>$done_msg</strong></p>";
    }
}

