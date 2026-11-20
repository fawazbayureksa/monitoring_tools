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

        // 1. Model-level validation: cegah duplikasi tool_id dalam satu request dan validasi kuantitas
        $seen_tools = [];
        foreach ($items as $item) {
            $t_id = (int)($item['tool_id'] ?? 0);
            $qty = (int)($item['qty'] ?? 0);

            if ($t_id <= 0 || $qty <= 0) {
                return false;
            }

            if (in_array($t_id, $seen_tools)) {
                // Duplikasi tool_id dalam satu permohonan dilarang
                return false;
            }
            $seen_tools[] = $t_id;
        }

        $this->db->trans_start();

        // 2. Cek ketersediaan dan kurangi stok untuk setiap alat dengan atomic update
        foreach ($items as $item) {
            $t_id = (int)$item['tool_id'];
            $qty = (int)$item['qty'];

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

        // 3. Simpan Header ke tabel requests
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

        // 4. Simpan Detail ke tabel request_items
        $batch_items = [];
        foreach ($items as $item) {
            $batch_items[] = [
                'request_id'     => $request_id,
                'tool_id'        => (int)$item['tool_id'],
                'qty_requested'  => (int)$item['qty'],
                'qty_returned'   => 0,
                'stock_restored' => 0,
                'created_at'     => date('Y-m-d H:i:s')
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

    /**
     * Mengembalikan stok seluruh item dalam request secara idempoten.
     * Menggunakan DB transaction dan row locking (SELECT ... FOR UPDATE)
     * untuk mencegah double restoration akibat multiple submit.
     */
    public function return_request_items_stock($request_id) {
        $request_id = (int)$request_id;
        if ($request_id <= 0) {
            return false;
        }

        $this->db->trans_begin();

        // 1. Kunci baris request
        $request = $this->db->query("SELECT * FROM requests WHERE id = ? FOR UPDATE", [$request_id])->row_array();
        if (!$request) {
            $this->db->trans_rollback();
            return false;
        }

        // 2. Kunci dan ambil item yang stoknya BELUM dikembalikan (stock_restored = 0)
        $items = $this->db->query("SELECT * FROM request_items WHERE request_id = ? AND stock_restored = 0 FOR UPDATE", [$request_id])->result_array();

        if (empty($items)) {
            // Cek jika request lama (legacy) yang belum memiliki baris di request_items
            $has_items = $this->db->get_where('request_items', ['request_id' => $request_id])->num_rows();
            if ($has_items == 0 && !empty($request['tool_id']) && !empty($request['qty_requested'])) {
                // Pastikan status request belum pernah final agar tidak dobel
                if (!in_array($request['status'], ['Declined', 'Cancelled', 'Completed', 'BAKK', 'BAKK (RUSAK)', 'BAKK (HILANG)'])) {
                    $this->return_tool_stock($request['tool_id'], $request['qty_requested']);
                }
            }
            $this->db->trans_commit();
            return true;
        }

        // 3. Kembalikan stok dan tandai stock_restored = 1
        foreach ($items as $item) {
            $tool_id = (int)$item['tool_id'];
            $qty = (int)$item['qty_requested'];
            if ($tool_id > 0 && $qty > 0) {
                $this->db->set('quantity', 'quantity + ' . $qty, FALSE);
                $this->db->where('id', $tool_id);
                $this->db->update('tools');

                $this->db->where('id', $item['id']);
                $this->db->update('request_items', [
                    'stock_restored' => 1,
                    'qty_returned'   => 0
                ]);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    /**
     * Membatalkan/menolak request secara idempoten dan mengembalikan stok seluruh item tepat satu kali.
     */
    public function decline_request($request_id, $notes = '') {
        $request_id = (int)$request_id;
        if ($request_id <= 0) {
            return false;
        }

        $this->db->trans_begin();

        $request = $this->db->query("SELECT * FROM requests WHERE id = ? FOR UPDATE", [$request_id])->row_array();
        if (!$request) {
            $this->db->trans_rollback();
            return false;
        }

        // Cegah penolakan berulang pada request yang sudah final
        if (in_array($request['status'], ['Declined', 'Cancelled', 'Completed', 'BAKK', 'BAKK (RUSAK)', 'BAKK (HILANG)'])) {
            $this->db->trans_rollback();
            return false;
        }

        // Kembalikan stok item yang belum di-restore
        $items = $this->db->query("SELECT * FROM request_items WHERE request_id = ? AND stock_restored = 0 FOR UPDATE", [$request_id])->result_array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $tool_id = (int)$item['tool_id'];
                $qty = (int)$item['qty_requested'];
                if ($tool_id > 0 && $qty > 0) {
                    $this->db->set('quantity', 'quantity + ' . $qty, FALSE);
                    $this->db->where('id', $tool_id);
                    $this->db->update('tools');

                    $this->db->where('id', $item['id']);
                    $this->db->update('request_items', [
                        'stock_restored' => 1,
                        'qty_returned'   => 0
                    ]);
                }
            }
        } else {
            // Legacy row fallback
            $has_items = $this->db->get_where('request_items', ['request_id' => $request_id])->num_rows();
            if ($has_items == 0 && !empty($request['tool_id']) && !empty($request['qty_requested'])) {
                $this->return_tool_stock($request['tool_id'], $request['qty_requested']);
            }
        }

        $data_update = ['status' => 'Declined'];
        if (!empty($notes)) {
            $data_update['notes'] = $notes;
        }
        $this->db->where('id', $request_id);
        $this->db->update('requests', $data_update);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    /**
     * Memproses inspeksi pengembalian alat per item secara atomik dalam satu database transaction.
     * Tidak ada default condition 'good', wajib eksplisit good/broken/lost.
     * Hanya item 'good' yang mengembalikan stok. Item broken/lost dicatat ke BAKK.
     */
    public function validate_return_inspection($request_id, $item_conditions, $item_notes = [], $general_notes = '') {
        $request_id = (int)$request_id;
        if ($request_id <= 0 || !is_array($item_conditions) || empty($item_conditions)) {
            return ['status' => false, 'message' => 'Data inspeksi tidak valid atau kosong.'];
        }

        $this->db->trans_begin();

        // 1. Kunci baris request
        $request = $this->db->query("SELECT * FROM requests WHERE id = ? FOR UPDATE", [$request_id])->row_array();
        if (!$request) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => 'Data permohonan tidak ditemukan.'];
        }

        if ($request['status'] !== 'Returned') {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => 'Permohonan harus berstatus Returned untuk dapat diinspeksi. Status saat ini: ' . $request['status']];
        }

        // 2. Kunci dan ambil seluruh item yang terdaftar pada request ini
        $req_items = $this->db->query("SELECT * FROM request_items WHERE request_id = ? FOR UPDATE", [$request_id])->result_array();
        if (empty($req_items)) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => 'Rincian item permohonan tidak ditemukan.'];
        }

        $valid_item_ids = array_column($req_items, 'id');

        // 3. Validasi ketat: Seluruh item ID yang dikirim harus valid dan cocok dengan request ini
        $submitted_item_ids = array_keys($item_conditions);
        foreach ($submitted_item_ids as $s_id) {
            if (!in_array($s_id, $valid_item_ids)) {
                $this->db->trans_rollback();
                return ['status' => false, 'message' => 'Item ID tidak valid atau tidak termasuk dalam permohonan ini.'];
            }
        }

        // Setiap item terdaftar WAJIB memiliki status kondisi yang eksplisit
        foreach ($valid_item_ids as $v_id) {
            if (!isset($item_conditions[$v_id])) {
                $this->db->trans_rollback();
                return ['status' => false, 'message' => 'Setiap alat wajib memiliki hasil inspeksi yang eksplisit.'];
            }
            $cond = trim($item_conditions[$v_id]);
            if (!in_array($cond, ['good', 'broken', 'lost'], true)) {
                $this->db->trans_rollback();
                return ['status' => false, 'message' => 'Kondisi alat tidak valid. Pilihan hanya Baik, Rusak, atau Hilang.'];
            }
        }

        // 4. Proses inspeksi item per item
        $has_broken_or_lost = false;
        foreach ($req_items as $item) {
            $item_id = $item['id'];
            $cond = trim($item_conditions[$item_id]);
            $note_item = isset($item_notes[$item_id]) ? trim($item_notes[$item_id]) : '';
            if (empty($note_item)) {
                $note_item = $general_notes;
            }

            if ($cond === 'good') {
                // Item BAIK: kembalikan stok hanya jika belum pernah di-restore
                if ($item['stock_restored'] == 0) {
                    $this->db->set('quantity', 'quantity + ' . (int)$item['qty_requested'], FALSE);
                    $this->db->where('id', $item['tool_id']);
                    $this->db->update('tools');
                }

                $this->db->where('id', $item_id);
                $this->db->update('request_items', [
                    'qty_returned'     => (int)$item['qty_requested'],
                    'return_condition' => 'good',
                    'stock_restored'   => 1
                ]);
            } else {
                // Item RUSAK atau HILANG: Jangan kembalikan stok, catat ke BAKK
                $has_broken_or_lost = true;
                $status_bakk = ($cond === 'broken') ? 'RUSAK' : 'HILANG';

                $this->db->where('id', $item_id);
                $this->db->update('request_items', [
                    'qty_returned'     => 0,
                    'return_condition' => $cond,
                    'stock_restored'   => 0
                ]);

                // Buat atau perbarui catatan di tabel bakk
                $existing_bakk = $this->db->get_where('bakk', [
                    'request_id' => $request_id,
                    'tool_id'    => $item['tool_id']
                ])->row_array();

                if ($existing_bakk) {
                    $this->db->where('id', $existing_bakk['id']);
                    $this->db->update('bakk', [
                        'condition_status' => $status_bakk,
                        'notes'            => $note_item
                    ]);
                } else {
                    $this->db->insert('bakk', [
                        'request_id'       => $request_id,
                        'tool_id'          => $item['tool_id'],
                        'condition_status' => $status_bakk,
                        'upload_status'    => 'Pending Upload',
                        'notes'            => $note_item
                    ]);
                }
            }
        }

        // 5. Update status header request
        $final_status = $has_broken_or_lost ? 'BAKK' : 'Completed';
        $req_update = [
            'status'             => $final_status,
            'return_date_actual' => date('Y-m-d H:i:s')
        ];
        if (!empty($general_notes)) {
            $req_update['notes'] = $general_notes;
        }

        $this->db->where('id', $request_id);
        $this->db->update('requests', $req_update);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => 'Gagal memperbarui status transaksi inspeksi di database.'];
        }

        $this->db->trans_commit();
        return ['status' => true, 'final_status' => $final_status];
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
            $this->db->where_in('status', ['Submitted', 'Returned', 'Waiting Approval', 'Approved']);
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
                } else if ($q['status'] == 'Approved' && ($role == 'master' || $role == 'admin')) {
                    $notifs[] = [
                        'icon' => 'fas fa-truck text-success',
                        'text' => $q['request_number'] . ' Approved - Need Shipping',
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