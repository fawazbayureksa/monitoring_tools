<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Auth');
    }
    public function index() {
       $data['title'] = 'Log In';
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        $this->load->view('login');
    }

    public function login() {
        $nrp = $this->input->post('nrp');
        $password = $this->input->post('password');

        $user = $this->M_Auth->check_login($nrp, $password);

        if ($user) {
            $session_data = array(
                'user_id'   => $user['id'],
                'nrp'       => $user['nrp'],
                'name'      => $user['name'],
                'role'      => $user['role'],
                'logged_in' => TRUE
            );
            $this->session->set_userdata($session_data);
            
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'ID atau Password salah!');
            redirect('auth');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }
}