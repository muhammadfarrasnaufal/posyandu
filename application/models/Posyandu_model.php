<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posyandu_model extends CI_Model {
    /**
     * @property CI_DB $db
     */

    public function get_all() {
        $this->db->select('r.*, u.fullname AS owner_name');
        $this->db->from('posyandu_records r');
        $this->db->join('users u', 'u.id = r.user_id', 'left');
        $this->db->order_by('r.tanggal_kunjungan', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_user($user_id) {
        $this->db->select('r.*, u.fullname AS owner_name');
        $this->db->from('posyandu_records r');
        $this->db->join('users u', 'u.id = r.user_id', 'left');
        $this->db->where('r.user_id', $user_id);
        $this->db->order_by('r.tanggal_kunjungan', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('posyandu_records', ['id' => $id])->row();
    }

    public function insert_record($data) {
        return $this->db->insert('posyandu_records', $data);
    }

    public function update_record($id, $data) {
        return $this->db->where('id', $id)->update('posyandu_records', $data);
    }

    public function delete_record($id) {
        return $this->db->where('id', $id)->delete('posyandu_records');
    }

    public function count_all() {
        return $this->db->count_all('posyandu_records');
    }

    public function count_today() {
        $this->db->where('DATE(created_at)', 'CURDATE()', FALSE);
        return $this->db->count_all_results('posyandu_records');
    }

    public function get_recent_records($limit = 10) {
        $this->db->select('r.*, u.fullname AS owner_name');
        $this->db->from('posyandu_records r');
        $this->db->join('users u', 'u.id = r.user_id', 'left');
        $this->db->order_by('r.updated_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

}
