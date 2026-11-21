<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title']     = 'About';
        $data['page_css']  = ['assets/app/css/pages/about.css'];
        $data['page_js']   = ['assets/app/js/pages/about.js'];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('About');
        $this->load->view('templates/footer', $data);
    }
}
