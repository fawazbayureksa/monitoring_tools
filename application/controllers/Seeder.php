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
}
