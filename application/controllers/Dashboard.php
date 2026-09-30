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

    if ($role == 'admin' || $role == 'master') { 
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
        $request_id = $this->input->post('request_id');
        $tool_id = $this->input->post('tool_id');
        $qty = $this->input->post('qty_requested');
        
        $action = $this->input->post('status_action'); 
        $notes = $this->input->post('notes');

    $data_update = array();
        
        if ($action == 'release') {
            // Toolkeeper melepas alat ke lapangan, leader yang konfirmasi terima di site
            $data_update['status'] = 'Waiting Received'; 
            $data_update['shipped_at'] = date('Y-m-d H:i:s');
        } 
        elseif ($action == 'receive') {
            // INI FUNGSI BARU: Leader menekan tombol Receive, status resmi dipinjam
            $data_update['status'] = 'Borrowed';
        }
        elseif ($action == 'send_supervisor') {
            $data_update['status'] = 'Waiting Approval';
        }
        elseif ($action == 'cancel') {
            $data_update['status'] = 'Declined';
            $data_update['notes'] = $notes;
            $this->M_Requests->return_tool_stock($tool_id, $qty);
        }
        elseif ($action == 'return') {
            $data_update['status'] = 'Returned';
            $data_update['return_date_actual'] = date('Y-m-d H:i:s');
        }
        elseif ($action == 'validate_return') {
            $condition = $this->input->post('condition');
            $data_update['return_date_actual'] = date('Y-m-d H:i:s');

            if ($condition == 'good') {
                $data_update['status'] = 'Completed'; 
                $this->M_Requests->return_tool_stock($tool_id, $qty);
            } else {
                $data_update['status'] = 'BAKK'; 
                $bakk_data = array(
                    'request_id' => $request_id,
                    'tool_id'    => $tool_id,
                    'condition_status' => ($condition == 'broken') ? 'RUSAK' : 'HILANG',
                    'upload_status' => 'Pending Upload',
                    'notes'      => $notes
                );
                $this->M_Requests->insert_bakk($bakk_data);
            }
            $data_update['notes'] = $notes;
        }

        // Simpan ke database
        if (!empty($data_update) && !empty($request_id)) {
            $this->M_Requests->update_request_status($request_id, $data_update);
        }

        redirect('dashboard');
        }

        public function mark_read($id) {
        $this->M_Requests->mark_notification_read($id);
    }

}