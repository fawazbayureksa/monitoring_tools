<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Requests');
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

        public function index() {
            
        $data['title'] = 'History';
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $nrp = $this->session->userdata('nrp'); 
        $data['total_stock'] = $this->db->count_all_results('tools');

        $data['total_borrowed'] = $this->M_Requests->count_active_requests($user_id, $nrp);
        $this->db->where('upload_status', 'Pending Upload');
        $data['total_bakk'] = $this->db->count_all_results('bakk');
        
        $date_range = $this->input->get('date_range');
        $start_date = null;
        $end_date = null;

        if (!empty($date_range)) {
            $dates = explode(' - ', $date_range);
            if (count($dates) == 2) {
                // Gunakan DateTime agar format d/m/Y terbaca sempurna
                $start = DateTime::createFromFormat('d/m/Y', trim($dates[0]));
                $end = DateTime::createFromFormat('d/m/Y', trim($dates[1]));
                
                if ($start && $end) {
                    $start_date = $start->format('Y-m-d');
                    $end_date = $end->format('Y-m-d');
                }
            }
        }

    $data['history'] = $this->M_Requests->get_history($start_date, $end_date, $user_id, $nrp);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('history', $data);
        $this->load->view('templates/footer');
    }

    public function download_pdf($request_id) {
        $request_id = (int)$request_id;
        if ($request_id <= 0) {
            redirect('history');
            return;
        }

        // 1. Ambil data request header
        $this->db->select('requests.*, users.name as user_name, users.nrp');
        $this->db->from('requests');
        $this->db->join('users', 'users.id = requests.user_id', 'left');
        $this->db->where('requests.id', $request_id);
        $request = $this->db->get()->row_array();

        if (!$request) {
            echo "<script>alert('Data permohonan tidak ditemukan.'); window.history.back();</script>";
            return;
        }

        // 2. Verifikasi hak akses (Security & Authorization)
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $nrp = $this->session->userdata('nrp');

        if ($role != 'master' && $role != 'admin' && $role != 'supervisor' && $role != 'spv') {
            if (strpos($nrp, 'mech_') !== false) {
                $location = str_replace('mech_', '', $nrp);
                $owner = $this->db->get_where('users', ['id' => $request['user_id']])->row_array();
                if (!$owner || $owner['nrp'] != 'leader_' . $location) {
                    redirect('dashboard');
                    return;
                }
            } elseif ($request['user_id'] != $user_id && $request['borrower_nrp'] != $nrp) {
                redirect('dashboard');
                return;
            }
        }

        // 3. Verifikasi status final (Availability Rule: Hanya untuk status Completed atau BAKK)
        $final_statuses = ['Completed', 'BAKK', 'BAKK (RUSAK)', 'BAKK (HILANG)'];
        if (!in_array($request['status'], $final_statuses)) {
            echo "<script>alert('Form Peminjaman PDF hanya dapat diunduh untuk permohonan yang telah selesai (Completed atau BAKK) setelah melalui seluruh proses inspeksi pengembalian.'); window.history.back();</script>";
            return;
        }

        // 4. Ambil seluruh item dalam request
        $items = $this->M_Requests->get_request_items($request_id);

        // 5. Inisialisasi library PDF dan generate output download
        $this->load->library('pdf_borrowing_form');
        $this->pdf_borrowing_form->set_data($request, $items);

        $clean_req_num = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request['request_number']);
        $filename = 'FORM-PEMINJAMAN-TOOLS-' . $clean_req_num . '.pdf';

        $this->pdf_borrowing_form->generate_pdf('D', $filename);
    }
}