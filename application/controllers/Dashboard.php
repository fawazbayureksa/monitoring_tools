<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('M_Requests'); 
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

public function index() {
    $data['title'] = 'Dashboard';
    $role = $this->session->userdata('role');
    $user_id = $this->session->userdata('user_id');
    $nrp = $this->session->userdata('nrp'); // Ambil nrp

    $data['total_stock'] = $this->db->count_all('tools');
    
    $this->db->where('upload_status', 'Pending Upload');
    $data['total_bakk'] = $this->db->count_all_results('bakk');
    
    $data['in_delivery'] = $this->M_Requests->get_in_delivery();

    if ($role == 'admin' || $role == 'master' || $role == 'supervisor' || $role == 'spv') { 
        $data['total_borrowed'] = $this->M_Requests->count_active_requests();
        $data['recent_requests'] = $this->M_Requests->get_recent_requests();
        $data['return_reminders'] = $this->M_Requests->get_return_reminders();
    } else {
        // Ganti parameter $role menjadi $nrp
        $data['total_borrowed'] = $this->M_Requests->count_active_requests($user_id, $nrp);
        $data['recent_requests'] = $this->M_Requests->get_user_requests($user_id, $nrp);
        $data['cancelled_alerts'] = $this->M_Requests->get_user_cancelled_alerts($user_id, $nrp); 
        $data['return_reminders'] = $this->M_Requests->get_return_reminders($user_id, $nrp);
    }

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('dashboard', $data);
    $this->load->view('templates/footer');
}
    
    public function process_action() {
        $request_id = (int)$this->input->post('request_id');
        $action = $this->input->post('status_action'); 
        $notes = $this->input->post('notes');
        $role = $this->session->userdata('role');
        $user_id_saat_ini = $this->session->userdata('user_id');

        if ($request_id <= 0 || empty($action)) {
            redirect('dashboard');
            return;
        }

        // Ambil data request saat ini untuk validasi status & authorization
        $request = $this->db->get_where('requests', ['id' => $request_id])->row_array();
        if (!$request) {
            echo "<script>alert('Data permohonan tidak ditemukan.'); window.history.back();</script>";
            return;
        }

        $data_update = array();

        // 1. ACTION: release (Pelepasan langsung untuk Workshop)
        if ($action == 'release') {
            if ($role != 'master' && $role != 'admin') {
                redirect('dashboard');
                return;
            }
            if ($request['status'] !== 'Submitted') {
                echo "<script>alert('Hanya permohonan dengan status Submitted yang dapat dirilis.'); window.history.back();</script>";
                return;
            }

            $data_update['status'] = 'Waiting Received'; 
            $data_update['shipped_at'] = date('Y-m-d H:i:s');
            if (!empty($notes)) {
                $data_update['notes'] = $notes;
            }
        }

        // 2. ACTION: send_supervisor (Admin memvalidasi ketersediaan dan mengajukan ke SPV untuk Site)
        elseif ($action == 'send_supervisor') {
            if ($role != 'master' && $role != 'admin') {
                redirect('dashboard');
                return;
            }
            if ($request['status'] !== 'Submitted') {
                echo "<script>alert('Permohonan tidak dalam status Submitted.'); window.history.back();</script>";
                return;
            }

            // Catatan: Admin tidak mengunggah resi di tahap ini
            $data_update['status'] = 'Waiting Approval';
            if (!empty($notes)) {
                $data_update['notes'] = $notes;
            }
        }

        // 3. ACTION: approve_spv (Supervisor menyetujui request secara keseluruhan)
        elseif ($action == 'approve_spv') {
            if ($role != 'master' && $role != 'supervisor' && $role != 'spv') {
                redirect('dashboard');
                return;
            }
            if ($request['status'] !== 'Waiting Approval') {
                echo "<script>alert('Permohonan tidak dalam status Waiting Approval.'); window.history.back();</script>";
                return;
            }

            // SPV menyetujui: request kembali ke Admin untuk proses pengiriman (status Approved)
            // SPV tidak mengunggah resi pengiriman
            $data_update['status'] = 'Approved';
            if (!empty($notes)) {
                $data_update['notes'] = $notes;
            }
        }

        // 4. ACTION: ship (Admin mengonfirmasi pengiriman dan mengunggah resi pengiriman ke Site)
        elseif ($action == 'ship') {
            if ($role != 'master' && $role != 'admin') {
                redirect('dashboard');
                return;
            }
            if ($request['status'] !== 'Approved') {
                echo "<script>alert('Permohonan harus telah disetujui Supervisor (Approved) untuk dikirim.'); window.history.back();</script>";
                return;
            }

            if (empty($_FILES['shipping_receipt']['name'])) {
                echo "<script>alert('Wajib mengunggah resi pengiriman (Gambar/PDF)!'); window.history.back();</script>";
                return;
            }

            $upload_path = './assets/uploads/shipping_receipt/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            $config_upload = [
                'upload_path'   => $upload_path,
                'allowed_types' => 'pdf|jpg|jpeg|png',
                'max_size'      => 5120,
                'file_name'     => 'RESI_' . $request_id . '_' . time()
            ];
            $this->load->library('upload', $config_upload);
            $this->upload->initialize($config_upload);

            if ($this->upload->do_upload('shipping_receipt')) {
                $up_data = $this->upload->data();
                $data_update['shipping_receipt'] = $up_data['file_name'];
            } else {
                $error = $this->upload->display_errors();
                echo "<script>alert('Gagal Upload Resi Pengiriman: " . strip_tags($error) . "'); window.history.back();</script>";
                return;
            }

            $data_update['status'] = 'Waiting Received';
            $data_update['shipped_at'] = date('Y-m-d H:i:s');
            if (!empty($notes)) {
                $data_update['notes'] = $notes;
            }
        }

        // 5. ACTION: receive (Peminjam / Leader konfirmasi penerimaan fisik alat di site)
        elseif ($action == 'receive') {
            $nrp = $this->session->userdata('nrp');
            $is_authorized = ($role == 'master' || $request['user_id'] == $user_id_saat_ini || (!empty($request['borrower_nrp']) && $request['borrower_nrp'] == $nrp));
            if (!$is_authorized) {
                redirect('dashboard');
                return;
            }
            if ($request['status'] !== 'Waiting Received') {
                echo "<script>alert('Permohonan tidak dalam status Waiting Received.'); window.history.back();</script>";
                return;
            }

            if (empty($_FILES['received_proof']['name'])) {
                echo "<script>alert('Wajib mengunggah bukti fisik penerimaan alat (Foto/PDF)!'); window.history.back();</script>";
                return;
            }

            $upload_path = './assets/uploads/received_proof/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            $config_upload = [
                'upload_path'   => $upload_path,
                'allowed_types' => 'pdf|jpg|jpeg|png',
                'max_size'      => 5120,
                'file_name'     => 'RECV_' . $request_id . '_' . time()
            ];
            $this->load->library('upload', $config_upload);
            $this->upload->initialize($config_upload);

            if ($this->upload->do_upload('received_proof')) {
                $up_data = $this->upload->data();
                $data_update['received_proof'] = $up_data['file_name'];
            } else {
                $error = $this->upload->display_errors();
                echo "<script>alert('Gagal Upload Bukti Penerimaan: " . strip_tags($error) . "'); window.history.back();</script>";
                return;
            }

            $received_notes = $this->input->post('received_notes');
            if (!empty($received_notes)) {
                $data_update['received_notes'] = $received_notes;
            }
            $data_update['received_at'] = date('Y-m-d H:i:s');
            $data_update['status'] = 'Borrowed';
        }

        // 6. ACTION: return (Leader / User mengembalikan alat ke gudang Toolkeeper)
        elseif ($action == 'return') {
            if ($request['status'] !== 'Borrowed' && $request['status'] !== 'OTW Site' && $request['status'] !== 'OTW Workshop') {
                echo "<script>alert('Permohonan tidak dalam status Borrowed.'); window.history.back();</script>";
                return;
            }

            $data_update['status'] = 'Returned';
            $data_update['return_date_actual'] = date('Y-m-d H:i:s');
        }

        // 7. ACTION: validate_return (Admin melakukan inspeksi fisik per item pengembalian)
        elseif ($action == 'validate_return') {
            if ($role != 'master' && $role != 'admin') {
                redirect('dashboard');
                return;
            }

            $item_conditions = $this->input->post('item_condition'); // Array [item_id => 'good'|'broken'|'lost']
            $item_notes = $this->input->post('item_notes');

            $inspection_result = $this->M_Requests->validate_return_inspection($request_id, $item_conditions, $item_notes, $notes);

            if (!$inspection_result['status']) {
                echo "<script>alert('" . addslashes($inspection_result['message']) . "'); window.history.back();</script>";
                return;
            }

            redirect('dashboard');
            return;
        }

        // 8. ACTION: cancel (Menolak / membatalkan request dan mengembalikan stok secara idempoten)
        elseif ($action == 'cancel') {
            // Role yang boleh menolak: Admin, SPV, Master, atau Leader pemilik request
            $is_allowed = ($role == 'master' || $role == 'admin' || $role == 'supervisor' || $role == 'spv' || $request['user_id'] == $user_id_saat_ini);
            if (!$is_allowed) {
                redirect('dashboard');
                return;
            }

            $decline_success = $this->M_Requests->decline_request($request_id, $notes);
            if (!$decline_success) {
                echo "<script>alert('Gagal membatalkan permohonan atau permohonan sudah berada dalam status akhir.'); window.history.back();</script>";
                return;
            }

            redirect('dashboard');
            return;
        }

        // Simpan perubahan data header ke database jika ada
        if (!empty($data_update)) {
            $this->M_Requests->update_request_status($request_id, $data_update);
        }

        redirect('dashboard');
    }

    public function mark_read($id) {
        $this->M_Requests->mark_notification_read($id);
    }

}