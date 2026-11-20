<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_tools extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
            $allowed_roles = ['admin', 'master', 'leader', 'leader_wsp', 'leader_field', 'leader_pml', 'leader_lga', 'leader_kdi', 'leader_mrw', 'leader_rta'];
            if (!in_array($this->session->userdata('role'), $allowed_roles)) {
            redirect('dashboard');
        }
        $this->load->model('M_Tools');
        $this->load->model('M_Requests');
        $this->load->helper(array('url', 'form'));
    }

    public function index() {
        $data['title'] = 'Request Tools';
        $data['req_number'] = $this->M_Requests->generate_request_number();
        
        $data['tools'] = $this->M_Requests->get_available_tools();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('Request_tools', $data);
        $this->load->view('templates/footer');
    }

    public function get_tool_info() {
        $tool_id = $this->input->post('tool_id');
        $tool = $this->M_Requests->get_tool_by_id($tool_id);
        echo json_encode($tool); 
    }

    public function submit() {
        $mechanic_input = $this->input->post('mechanic_data'); 
        $mech_parts = explode('|', $mechanic_input);
        $borrower_nrp = isset($mech_parts[0]) ? trim($mech_parts[0]) : '';
        $borrower_name = isset($mech_parts[1]) ? strtoupper(trim($mech_parts[1])) : $borrower_nrp;

        // Parse items (mendukung array multi-tools maupun legacy single-tool)
        $items = [];
        $tool_ids = $this->input->post('tool_ids');
        $qtys = $this->input->post('qtys');

        if (!empty($tool_ids) && is_array($tool_ids)) {
            $seen = [];
            foreach ($tool_ids as $idx => $tid) {
                $tid = (int)$tid;
                $q = isset($qtys[$idx]) ? (int)$qtys[$idx] : 1;
                if ($tid > 0 && $q > 0) {
                    if (isset($seen[$tid])) {
                        echo "<script>alert('Gagal: Terdapat alat duplikat dalam permohonan!'); window.history.back();</script>";
                        return;
                    }
                    $seen[$tid] = true;
                    $items[] = [
                        'tool_id' => $tid,
                        'qty'     => $q
                    ];
                }
            }
        } elseif ($this->input->post('tool_id')) {
            // Legacy single-tool
            $items[] = [
                'tool_id' => (int)$this->input->post('tool_id'),
                'qty'     => (int)$this->input->post('qty') ?: 1
            ];
        }

        if (empty($items)) {
            echo "<script>alert('Wajib memilih minimal satu alat!'); window.history.back();</script>";
            return;
        }

        $first_tool = $items[0];

        $data = array(
            'request_number' => $this->input->post('request_number'),
            'user_id'        => $this->session->userdata('user_id'),
            'tool_id'        => $first_tool['tool_id'], // Legacy fallback
            'borrower_name'  => $borrower_name, 
            'borrower_nrp'   => $borrower_nrp,
            'qty_requested'  => $first_tool['qty'], // Legacy fallback
            'site_location'  => $this->input->post('site_location'),
            'borrow_date'    => $this->input->post('borrow_date'), 
            'return_date_est'=> $this->input->post('return_date'), 
            'status'         => 'Submitted'
        );

        if ($this->M_Requests->process_request($data, $items)) {
            redirect('dashboard'); 
        } else {
            echo "<script>alert('Gagal memproses peminjaman. Pastikan stok mencukupi untuk semua alat yang dipilih.'); window.history.back();</script>";
        }
    }
}