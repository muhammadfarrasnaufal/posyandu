<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    /**
     * @property CI_DB $db
     */

    public function get_by_email($email) {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function get_all() {
        return $this->db->order_by('created_at', 'DESC')->get('users')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function get_non_admin() {
        return $this->db->where('role', 'user')->order_by('fullname', 'ASC')->get('users')->result();
    }

    public function create_user($data) {
        return $this->db->insert('users', $data);
    }

    public function delete_user($id) {
        return $this->db->delete('users', ['id' => $id]);
    }

    public function email_exists($email) {
        return $this->db->where('email', $email)->count_all_results('users') > 0;
    }

    public function email_exists_except($email, $except_id) {
        return $this->db->where('email', $email)
                        ->where('id !=', $except_id)
                        ->count_all_results('users') > 0;
    }

    public function update_user($id, $data) {
        return $this->db->where('id', $id)->update('users', $data);
    }

}
