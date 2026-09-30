<?php
class User_management extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') != 'master') {
            redirect('dashboard');
        }
        $this->load->model('M_User');
    }

    public function index() {
        $data['title'] = 'User Management';
        $data['users'] = $this->M_User->get_all_users();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('user_management', $data);
        $this->load->view('templates/footer');
    }

    public function add() {
        $data = [
            'name'     => $this->input->post('name'),
            'nrp'      => $this->input->post('nrp'),

            'password' => $this->input->post('password'),
            'role'     => $this->input->post('role')
        ];
        $this->M_User->insert_user($data);
        redirect('user_management');
    }

        public function edit() {
        $id = $this->input->post('id');
        
        $data = [
            'name'     => $this->input->post('name'),
            'nrp'      => $this->input->post('nrp'),
            'password' => $this->input->post('password'),
            'role'     => $this->input->post('role')
        ];
        
        $this->M_User->update_user($id, $data);
        redirect('user_management');
    }

    public function delete($id) {
        $this->M_User->delete_user($id);
        redirect('user_management');
    }
}