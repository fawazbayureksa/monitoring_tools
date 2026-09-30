<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_tools extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        if ($this->session->userdata('role') != 'admin' && $this->session->userdata('role') != 'master') {
            redirect('dashboard'); 
        }
        $this->load->model('M_Tools');
        $this->load->model('M_Requests'); 
    }

    public function index() {
        $data['title'] = 'Stock Tools';
        $data['total_stock'] = $this->db->count_all('tools');

        $data['total_borrowed'] = $this->M_Requests->count_active_requests(); 
        
        $this->db->where('upload_status', 'Pending Upload');
        $data['total_bakk'] = $this->db->count_all_results('bakk');
        
        $data['tools'] = $this->M_Tools->get_all_tools();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('Stock_tools', $data);
        $this->load->view('templates/footer');
    }

    public function add() {
        $data = array(
            'tool_name'     => $this->input->post('tool_name'),
            'part_number'   => $this->input->post('part_number'),
            'type'          => $this->input->post('type'),
            'rack_location' => $this->input->post('location'),
            'quantity'      => $this->input->post('quantity')
        );

        $this->M_Tools->insert_tool($data);
        redirect('Stock_tools');
    }

public function import() {
        if (isset($_FILES['file_csv']['name'])) {
            $path = $_FILES['file_csv']['tmp_name'];
            
            $file = fopen($path, "r");
            $is_header = true;
            $data_tools = array();

            while (($row = fgetcsv($file, 10000, ";")) !== FALSE) {

                if ($is_header) {
                    $is_header = false;
                    continue;
                }
                if (count($row) < 5) {
                    continue;
                }

                $data_tools[] = array(
                    'tool_name'     => trim($row[0]), 
                    'part_number'   => trim($row[1]), 
                    'type'          => trim($row[2]), 
                    'rack_location' => trim($row[3]), 
                    'quantity'      => (int) trim($row[4]) 
                );
            }
            fclose($file);

            if (!empty($data_tools)) {
                $this->M_Tools->insert_batch_tools($data_tools);
            }
            
            redirect('Stock_tools');
        }
    }
    public function update() {
        $id = $this->input->post('id');
        
        $data = array(
            'tool_name'     => $this->input->post('tool_name'),
            'part_number'   => $this->input->post('part_number'),
            'type'          => $this->input->post('type'),
            'rack_location' => $this->input->post('location'),
            'quantity'      => $this->input->post('quantity')
        );

        $this->M_Tools->update_tool($id, $data);
        redirect('Stock_tools');
    }

    public function delete($id) {
        $this->M_Tools->delete_tool($id);
        redirect('Stock_tools');
    }
}