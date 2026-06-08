<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule_model extends CI_Model {
    /**
     * @property CI_DB $db
     */

    public function get_all() {
        $this->db->select('s.*, u.fullname AS owner_name, u.email AS owner_email');
        $this->db->from('immunization_schedules s');
        $this->db->join('users u', 'u.id = s.user_id', 'left');
        $this->db->order_by('s.jadwal', 'ASC');
        return $this->db->get()->result();
    }

    public function get_by_user($user_id) {
        return $this->db->where('user_id', $user_id)->order_by('jadwal', 'ASC')->get('immunization_schedules')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('immunization_schedules', ['id' => $id])->row();
    }

    public function insert_schedule($data) {
        return $this->db->insert('immunization_schedules', $data);
    }

    public function update_schedule($id, $data) {
        return $this->db->where('id', $id)->update('immunization_schedules', $data);
    }

    public function delete_schedule($id) {
        return $this->db->where('id', $id)->delete('immunization_schedules');
    }

    public function count_upcoming($days = 30) {
        $this->db->where('jadwal >=', date('Y-m-d'));
        $this->db->where('jadwal <=', date('Y-m-d', strtotime("+{$days} days")));
        return $this->db->count_all_results('immunization_schedules');
    }

    public function get_upcoming($days = 14, $limit = 5) {
        $today = date('Y-m-d');
        $this->db->select('s.*, u.fullname AS owner_name, u.email AS owner_email');
        $this->db->from('immunization_schedules s');
        $this->db->join('users u', 'u.id = s.user_id', 'left');
        $this->db->where('s.jadwal >=', $today);
        $this->db->where('s.jadwal <=', date('Y-m-d', strtotime("+{$days} days")));
        $this->db->order_by('s.jadwal', 'ASC');
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }

}
