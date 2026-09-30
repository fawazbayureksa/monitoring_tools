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
    
}