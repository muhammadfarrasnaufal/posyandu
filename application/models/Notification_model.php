<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {

    public function create($data) {
        return $this->db->insert('notifications', $data);
    }

    public function get_recent($since_id = 0) {
        if ($since_id > 0) {
            $this->db->where('id >', $since_id);
        }
        $this->db->order_by('id', 'ASC');
        return $this->db->get('notifications')->result();
    }

}
