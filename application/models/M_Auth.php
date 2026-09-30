<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Auth extends CI_Model {

    public function check_login($nrp, $password) {
        $this->db->where('nrp', $nrp);
        $this->db->where('password', $password);
        return $this->db->get('users')->row_array();
    }
}