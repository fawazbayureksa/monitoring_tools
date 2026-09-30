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
        $tool_id = $this->input->post('tool_id');
        $qty = $this->input->post('qty');
        $mechanic_input = $this->input->post('mechanic_data'); 
        $mech_parts = explode('|', $mechanic_input);
        $data = array(
            'request_number' => $this->input->post('request_number'),
            'user_id'        => $this->session->userdata('user_id'), // ID akun Leader yang login
            'tool_id'        => $tool_id,
            'borrower_name'  => strtoupper($mech_parts[0]), 
            'borrower_nrp'   => isset($mech_parts[1]) ? $mech_parts[1] : '',
            'qty_requested'  => $qty,
            'site_location'  => $this->input->post('site_location'),
            'borrow_date'    => $this->input->post('borrow_date'), 
            'return_date_est'=> $this->input->post('return_date'), 
            'status'         => 'Submitted'
        );

        if ($this->M_Requests->process_request($data, $tool_id, $qty)) {
            redirect('dashboard'); 
        } else {
            echo "Gagal memproses peminjaman.";
        }
    }
}