<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bakk extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        if ($this->session->userdata('role') != 'admin' && $this->session->userdata('role') != 'master') {
            redirect('dashboard'); 
        }

        $this->load->model('M_Requests');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index() {
        $data['title'] = 'BAKK';
        $data['total_stock'] = $this->db->count_all('tools');
        $data['total_borrowed'] = $this->M_Requests->count_active_requests();
        $this->db->where('upload_status', 'Pending Upload');
        $data['total_bakk'] = $this->db->count_all_results('bakk');
        
        $data['bakk_list'] = $this->M_Requests->get_bakk_tools();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('bakk', $data);
        $this->load->view('templates/footer');
    }

    public function upload_form($bakk_id) {
        $data['title'] = 'Upload BAKK';
        $data['bakk_id'] = $bakk_id;
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('bakk_upload', $data);
        $this->load->view('templates/footer');
    }

public function process_upload() {
        $bakk_id = $this->input->post('bakk_id'); 
        $notes = $this->input->post('notes');
        
        $config['upload_path']   = './assets/uploads/bakk/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['max_size']      = 5120;
        $config['file_name']     = 'BAKK_' . time();

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('berkas_bakk')) {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];

            $bakk = $this->db->get_where('bakk', ['id' => $bakk_id])->row_array();
            
            if ($bakk) {
                $request_id = $bakk['request_id'];
                $condition = $bakk['condition_status']; 

                $this->db->where('id', $bakk_id);
                $this->db->update('bakk', [
                    'upload_status' => 'Uploaded',
                    'file_path'     => $file_name,
                    'notes'         => $notes
                ]);

                $this->db->where('id', $request_id);
                $this->db->update('requests', [
                    'status' => 'BAKK (' . $condition . ')'
                ]);
            }
            redirect('history'); 
            
        } else {
            $error = $this->upload->display_errors();
            echo "<script>alert('Gagal Upload: " . strip_tags($error) . "'); window.history.back();</script>";
        }
    }
}