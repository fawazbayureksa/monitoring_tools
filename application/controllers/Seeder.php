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
}
