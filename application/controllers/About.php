<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index()
    {
        $data['title'] = 'About'; 
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('about'); 
        $this->load->view('templates/footer');
    }
}