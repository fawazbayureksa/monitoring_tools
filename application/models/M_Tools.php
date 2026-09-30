<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Tools extends CI_Model {

    public function get_all_tools() {

        $this->db->order_by('id', 'DESC');
        return $this->db->get('tools')->result_array();
    }

    public function insert_tool($data) {
        return $this->db->insert('tools', $data);
    }

    public function insert_batch_tools($data) {
        return $this->db->insert_batch('tools', $data);
    }

    public function update_tool($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tools', $data);
    }

    public function delete_tool($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tools');
    }
}