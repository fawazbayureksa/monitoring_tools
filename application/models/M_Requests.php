<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Requests extends CI_Model {

    public function get_available_tools() {
        $this->db->where('quantity >', 0);
        return $this->db->get('tools')->result_array();
    }

    public function get_users() {
        return $this->db->get('users')->result_array();
    }

    public function get_tool_by_id($id) {
        return $this->db->get_where('tools', ['id' => $id])->row_array();
    }

public function generate_request_number() {
        $role = $this->session->userdata('role');
        $nrp = $this->session->userdata('nrp'); 
        $kode_akun = 'OTH'; 
        
        if ($role == 'master' || $role == 'MASTER') {
            $kode_akun = 'MST'; 
            
        } elseif ($role == 'admin' || $role == 'ADMIN') {
            if (strpos($nrp, 'tkp_') !== false) {
                $site = strtoupper(str_replace('tkp_', '', $nrp));
                $kode_akun = 'TKP_' . $site; 
            } else {
                $kode_akun = 'TKP';
            }
            
        } elseif ($role == 'leader' || $role == 'LEADER MECHANIC') { 
            if (strpos($nrp, 'leader_') !== false) {
                $site = strtoupper(str_replace('leader_', '', $nrp));
                $kode_akun = 'LDR_' . $site; 
            } else {
                $kode_akun = 'LDR'; 
            }
        }
        
        $date_prefix = 'REQ-' . $kode_akun . "-" . date('Ymd') . '-';
        $this->db->like('request_number', $date_prefix);
        $this->db->order_by('id', 'DESC');
        $last_request = $this->db->get('requests', 1)->row_array();

        if ($last_request) {
            $last_number = explode('-', $last_request['request_number'])[3]; 
            $next_number = sprintf('%03d', intval($last_number) + 1);
        } else {
            $next_number = '001';
        }
        
        return $date_prefix . $next_number;
    }

    public function process_request($data, $tool_items, $legacy_qty = null) {
        // Normalisasi parameter untuk mendukung backward compatibility
        if (is_array($tool_items)) {
            $items = $tool_items;
        } else {
            $items = [
                ['tool_id' => (int)$tool_items, 'qty' => (int)$legacy_qty]
            ];
        }

        if (empty($items)) {
            return false;
        }

        $this->db->trans_start();

        // 1. Cek ketersediaan dan kurangi stok untuk setiap alat dengan atomic update
        foreach ($items as $item) {
            $t_id = (int)$item['tool_id'];
            $qty = (int)$item['qty'];

            if ($t_id <= 0 || $qty <= 0) {
                $this->db->trans_rollback();
                return false;
            }

            // Kurangi stok hanya jika stok saat ini mencukupi (prevent negative stock & race conditions)
            $this->db->set('quantity', 'quantity - ' . $qty, FALSE);
            $this->db->where('id', $t_id);
            $this->db->where('quantity >=', $qty);
            $this->db->update('tools');

            if ($this->db->affected_rows() <= 0) {
                // Stok tidak mencukupi atau alat tidak ditemukan
                $this->db->trans_rollback();
                return false;
            }
        }

        // 2. Simpan Header ke tabel requests
        // Isi fallback legacy tool_id & qty_requested dengan item pertama
        if (!isset($data['tool_id']) && !empty($items[0]['tool_id'])) {
            $data['tool_id'] = (int)$items[0]['tool_id'];
        }
        if (!isset($data['qty_requested']) && !empty($items[0]['qty'])) {
            $data['qty_requested'] = (int)$items[0]['qty'];
        }

        $this->db->insert('requests', $data);
        $request_id = $this->db->insert_id();

        if (!$request_id) {
            $this->db->trans_rollback();
            return false;
        }

        // 3. Simpan Detail ke tabel request_items
        $batch_items = [];
        foreach ($items as $item) {
            $batch_items[] = [
                'request_id'    => $request_id,
                'tool_id'       => (int)$item['tool_id'],
                'qty_requested' => (int)$item['qty'],
                'qty_returned'  => 0,
                'created_at'    => date('Y-m-d H:i:s')
            ];
        }
        $this->db->insert_batch('request_items', $batch_items);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get_request_items($request_id) {
        $this->db->select('request_items.*, tools.tool_name, tools.part_number, tools.type, tools.rack_location');
        $this->db->from('request_items');
        $this->db->join('tools', 'tools.id = request_items.tool_id');
        $this->db->where('request_items.request_id', $request_id);
        $items = $this->db->get()->result_array();

        if (empty($items)) {
            // Fallback jika data lama belum termigrasi di request_items
            $req = $this->db->get_where('requests', ['id' => $request_id])->row_array();
            if ($req && !empty($req['tool_id'])) {
                $tool = $this->get_tool_by_id($req['tool_id']);
                if ($tool) {
                    $items[] = [
                        'id' => 0,
                        'request_id' => $request_id,
                        'tool_id' => $tool['id'],
                        'tool_name' => $tool['tool_name'],
                        'part_number' => $tool['part_number'],
                        'type' => $tool['type'] ?? '',
                        'rack_location' => $tool['rack_location'] ?? '',
                        'qty_requested' => $req['qty_requested'] ?? 1,
                        'qty_returned' => ($req['status'] == 'Completed') ? ($req['qty_requested'] ?? 1) : 0,
                        'return_condition' => ($req['status'] == 'Completed') ? 'good' : null
                    ];
                }
            }
        }
        return $items;
    }

    public function attach_items_to_requests($requests) {
        if (empty($requests)) {
            return [];
        }

        $request_ids = array_column($requests, 'id');
        $this->db->select('request_items.*, tools.tool_name, tools.part_number, tools.type, tools.rack_location');
        $this->db->from('request_items');
        $this->db->join('tools', 'tools.id = request_items.tool_id');
        $this->db->where_in('request_items.request_id', $request_ids);
        $all_items = $this->db->get()->result_array();

        $grouped = [];
        foreach ($all_items as $item) {
            $grouped[$item['request_id']][] = $item;
        }

        foreach ($requests as &$req) {
            if (isset($grouped[$req['id']]) && !empty($grouped[$req['id']])) {
                $req['items'] = $grouped[$req['id']];
            } else {
                // Fallback untuk baris legacy
                $req['items'] = [
                    [
                        'id' => 0,
                        'request_id' => $req['id'],
                        'tool_id' => $req['tool_id'] ?? 0,
                        'tool_name' => $req['tool_name'] ?? 'Tool',
                        'part_number' => $req['part_number'] ?? '-',
                        'type' => $req['type'] ?? '',
                        'rack_location' => $req['rack_location'] ?? '',
                        'qty_requested' => $req['qty_requested'] ?? 1,
                        'qty_returned' => ($req['status'] == 'Completed') ? ($req['qty_requested'] ?? 1) : 0,
                        'return_condition' => null
                    ]
                ];
            }
            $req['total_items'] = count($req['items']);
            $req['total_qty'] = array_sum(array_column($req['items'], 'qty_requested'));
        }
        unset($req);

        return $requests;
    }

    public function return_request_items_stock($request_id) {
        $items = $this->get_request_items($request_id);
        if (!empty($items)) {
            $this->db->trans_start();
            foreach ($items as $item) {
                if (!empty($item['tool_id']) && !empty($item['qty_requested'])) {
                    $this->return_tool_stock($item['tool_id'], $item['qty_requested']);
                }
            }
            $this->db->trans_complete();
            return $this->db->trans_status();
        }
        return false;
    }

    public function update_request_item($item_id, $data) {
        $this->db->where('id', $item_id);
        return $this->db->update('request_items', $data);
    }

    public function get_recent_requests() {
        $this->db->select('requests.*, tools.tool_name, tools.part_number, users.name as user_name, users.nrp');
        $this->db->from('requests');
        $this->db->join('tools', 'tools.id = requests.tool_id', 'left');
        $this->db->join('users', 'users.id = requests.user_id', 'left');
        
        $status_selesai = ['Completed', 'Declined', 'BAKK (RUSAK)', 'BAKK (HILANG)'];
        $this->db->where_not_in('requests.status', $status_selesai);

        $this->db->order_by('requests.created_at', 'DESC');
        $this->db->order_by('requests.id', 'DESC');
        $this->db->limit(10);
        
        $requests = $this->db->get()->result_array();
        return $this->attach_items_to_requests($requests);
    }

    public function update_request_status($request_id, $data) {
        $this->db->where('id', $request_id);
        return $this->db->update('requests', $data);
    }

    public function return_tool_stock($tool_id, $qty) {
        $this->db->set('quantity', 'quantity + ' . (int)$qty, FALSE);
        $this->db->where('id', $tool_id);
        return $this->db->update('tools');
    }

    public function get_user_requests($user_id, $nrp = '') {
        $this->db->select('requests.*, tools.tool_name, tools.part_number, users.name as user_name, users.nrp');
        $this->db->from('requests');
        $this->db->join('tools', 'tools.id = requests.tool_id', 'left');
        $this->db->join('users', 'users.id = requests.user_id', 'left'); 
        
        $status_selesai = ['Completed', 'BAKK (RUSAK)', 'BAKK (HILANG)', 'Declined'];
        $this->db->where_not_in('requests.status', $status_selesai);

        if (strpos($nrp, 'mech_') !== false) {
            $location = str_replace('mech_', '', $nrp);
            $this->db->where('users.nrp', 'leader_' . $location);
        } else {
            $this->db->where('requests.user_id', $user_id);
        }
        $this->db->order_by('requests.created_at', 'DESC');
        $this->db->order_by('requests.id', 'DESC');
        $requests = $this->db->get()->result_array();
        return $this->attach_items_to_requests($requests);
    }

    // Monitoring pengiriman: request yang sudah dirilep tapi belum dikonfirmasi penerima
    public function get_in_delivery() {
        $this->db->select('requests.id, requests.request_number, requests.site_location, requests.status, requests.shipped_at, requests.shipping_receipt, requests.user_id, requests.tool_id, requests.qty_requested, tools.tool_name, tools.part_number, users.name as user_name, users.nrp, TIMESTAMPDIFF(HOUR, COALESCE(requests.shipped_at, requests.created_at), NOW()) as hours_in_delivery', FALSE);
        $this->db->from('requests');
        $this->db->join('tools', 'tools.id = requests.tool_id', 'left');
        $this->db->join('users', 'users.id = requests.user_id', 'left');
        $this->db->where_in('requests.status', ['Waiting Received', 'OTW Site', 'OTW Workshop']);
        $this->db->order_by('hours_in_delivery', 'DESC');
        $requests = $this->db->get()->result_array();
        return $this->attach_items_to_requests($requests);
    }

    // Reminder pengembalian: sudah dipinjam dan mendekati/melewati EST RETURN
    public function get_return_reminders($user_id = null, $nrp = '') {
        $days = (int)($this->config->item('return_reminder_days') ?: 3);

        $this->db->select('requests.id, requests.request_number, requests.site_location, requests.borrower_name, requests.borrower_nrp, requests.return_date_est, tools.tool_name, tools.part_number, TIMESTAMPDIFF(HOUR, NOW(), requests.return_date_est) as hours_left', FALSE);
        $this->db->from('requests');
        $this->db->join('tools', 'tools.id = requests.tool_id', 'left');
        $this->db->where('requests.status', 'Borrowed');
        $this->db->where('requests.return_date_est <=', 'DATE_ADD(NOW(), INTERVAL ' . $days . ' DAY)', FALSE);

        if ($user_id !== null) {
            if (strpos($nrp, 'mech_') !== false) {
                $location = str_replace('mech_', '', $nrp);
                $this->db->join('users', 'users.id = requests.user_id', 'left');
                $this->db->where('users.nrp', 'leader_' . $location);
            } else {
                $this->db->where('requests.user_id', $user_id);
            }
        }

        $this->db->order_by('requests.return_date_est', 'ASC');
        $requests = $this->db->get()->result_array();
        return $this->attach_items_to_requests($requests);
    }

    public function insert_bakk($data) {
        return $this->db->insert('bakk', $data);
    }

    public function get_history($start_date = null, $end_date = null, $user_id = null, $nrp = '') {
        $this->db->select('requests.*, tools.tool_name, tools.part_number, users.name as user_name, users.nrp, bakk.file_path, bakk.notes as bakk_notes, bakk.upload_status');
        $this->db->from('requests');
        $this->db->join('tools', 'tools.id = requests.tool_id', 'left');
        $this->db->join('users', 'users.id = requests.user_id', 'left');

        $this->db->join('bakk', 'bakk.request_id = requests.id', 'left'); 
        
        $status_selesai = ['Completed', 'Cancelled', 'BAKK', 'BAKK (RUSAK)', 'BAKK (HILANG)', 'Declined'];
        $this->db->where_in('requests.status', $status_selesai);
        
        if ($start_date !== null && $end_date !== null) {
            $this->db->where('requests.borrow_date >=', $start_date . ' 00:00:00');
            $this->db->where('requests.borrow_date <=', $end_date . ' 23:59:59');
        }

        if (strpos($nrp, 'mech_') !== false) {
            $location = str_replace('mech_', '', $nrp);
            $this->db->where('users.nrp', 'leader_' . $location);
        } elseif (strpos($nrp, 'leader_') !== false || $nrp == 'leader') {
            $this->db->where('requests.user_id', $user_id);
        } 
        $this->db->order_by('requests.id', 'DESC');
        $requests = $this->db->get()->result_array();
        return $this->attach_items_to_requests($requests);
    }

    public function get_bakk_tools() {
        $this->db->select('bakk.*, tools.tool_name, tools.part_number, tools.type, tools.rack_location, COALESCE(request_items.qty_requested, requests.qty_requested, 1) as qty_requested', FALSE);
        $this->db->from('bakk');
        $this->db->join('tools', 'tools.id = bakk.tool_id');
        $this->db->join('requests', 'requests.id = bakk.request_id', 'left');
        $this->db->join('request_items', 'request_items.request_id = bakk.request_id AND request_items.tool_id = bakk.tool_id', 'left');
        $this->db->where('bakk.upload_status', 'Pending Upload');
        $this->db->order_by('bakk.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function count_active_requests($user_id = null, $nrp = '') {
        $status_selesai = ['Completed', 'BAKK (RUSAK)', 'BAKK (HILANG)', 'Declined'];
        
        $this->db->select('requests.id');
        $this->db->from('requests');
        $this->db->where_not_in('requests.status', $status_selesai);

        if (strpos($nrp, 'mech_') !== false) {
            $location = str_replace('mech_', '', $nrp);
            $this->db->join('users', 'users.id = requests.user_id');
            $this->db->where('users.nrp', 'leader_' . $location);
        } elseif ($user_id !== null) {
            $this->db->where('requests.user_id', $user_id);
        }
        return $this->db->count_all_results();
    }

    public function get_user_cancelled_alerts($user_id, $nrp = '') {
        $this->db->select('requests.id, requests.request_number, requests.notes');
        $this->db->from('requests');
        $this->db->join('users', 'users.id = requests.user_id');
        $this->db->where('requests.status', 'Declined');
        $this->db->where('requests.is_read', 0); 
        
        if (strpos($nrp, 'mech_') !== false) {
            $location = str_replace('mech_', '', $nrp);
            $this->db->where('users.nrp', 'leader_' . $location);
        } else {
            $this->db->where('requests.user_id', $user_id);
        }
        $this->db->order_by('requests.id', 'DESC');
        $this->db->limit(3);
        return $this->db->get()->result_array();
    }

    public function get_notifications() {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        
        $notifs = [];
        
        if ($role == 'master' || $role == 'admin' || $role == 'supervisor' || $role == 'spv') { 
            $this->db->where_in('status', ['Submitted', 'Returned', 'Waiting Approval']);
            $this->db->order_by('id', 'DESC');
            $query = $this->db->get('requests')->result_array();
            
            foreach($query as $q) {
                if ($q['status'] == 'Submitted') {
                    $notifs[] = [
                        'icon' => 'fas fa-paper-plane text-primary',
                        'text' => $q['request_number'] . ' Need Release',
                        'link' => site_url('dashboard')
                    ];
                } else if ($q['status'] == 'Returned') {
                    $notifs[] = [
                        'icon' => 'fas fa-clipboard-check text-info',
                        'text' => $q['request_number'] . ' Need Validation',
                        'link' => site_url('dashboard')
                    ];
                } else if ($q['status'] == 'Waiting Approval' && ($role == 'master' || $role == 'supervisor' || $role == 'spv')) {
                    $notifs[] = [
                        'icon' => 'fas fa-user-check text-warning',
                        'text' => $q['request_number'] . ' Need SPV Approval',
                        'link' => site_url('dashboard')
                    ];
                }
            }
        } else {
            $nrp = '';
            if ($role == 'user' || $role == 'leader') {
                $current_user = $this->db->get_where('users', ['id' => $user_id])->row_array();
                $nrp = isset($current_user['nrp']) ? $current_user['nrp'] : '';
            }

            $this->db->where('status', 'Declined');
            $this->db->where('is_read', 0);
            
            if ($role == 'user' || $role == 'user_mechanic') {
                $this->db->where('borrower_nrp', $nrp);
            } else {
                $this->db->where('user_id', $user_id);
            }
            
            $this->db->order_by('id', 'DESC');
            $this->db->limit(5);
            $query = $this->db->get('requests')->result_array();
            
            foreach($query as $q) {
                $notifs[] = [
                    'icon' => 'fas fa-ban text-danger',
                    'text' => $q['request_number'] . ' Ditolak',
                    'link' => site_url('dashboard')
                ];
            }
        }
        
        return [
            'count' => count($notifs),
            'data' => $notifs
        ];
    }

    public function mark_notification_read($id) {
        $this->db->where('id', $id);
        return $this->db->update('requests', ['is_read' => 1]);
    }
}