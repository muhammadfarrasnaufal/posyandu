<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Loader $load
 * @property User_model $User_model
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
        $this->load->model('User_model');
    }

    private function is_api_request() {
        $acceptHeader = $this->input->get_request_header('Accept', TRUE);
        $xhrHeader = $this->input->get_request_header('X-Requested-With', TRUE);

        return (
            !empty($acceptHeader) && stripos($acceptHeader, 'application/json') !== false
        ) || $xhrHeader === 'XMLHttpRequest';
    }

    private function json_response($payload, $status = 200) {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($payload));
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect($this->session->userdata('role'));
        }

        $this->load->view('auth/login');
    }

    public function login() {
        if ($this->session->userdata('logged_in')) {
            if ($this->is_api_request()) {
                $this->json_response(['success' => true, 'redirect' => $this->session->userdata('role')]);
                return;
            }
            redirect($this->session->userdata('role'));
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            if ($this->is_api_request()) {
                $this->json_response(['success' => false, 'message' => validation_errors()], 400);
                return;
            }

            $this->load->view('auth/login');
            return;
        }

        $email = $this->input->post('email', TRUE);
        $password = $this->input->post('password');

        $user = $this->User_model->get_by_email($email);

        if ($user && password_verify($password, $user->password)) {
            $sessionData = [
                'user_id' => $user->id,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'role' => $user->role,
                'logged_in' => TRUE,
            ];
            $this->session->set_userdata($sessionData);

            if ($this->is_api_request()) {
                $this->json_response(['success' => true, 'redirect' => $user->role, 'role' => $user->role]);
                return;
            }

            redirect($user->role);
            return;
        }

        if ($this->is_api_request()) {
            $this->json_response(['success' => false, 'message' => 'Email atau password salah.'], 401);
            return;
        }

        $data['error'] = 'Email atau password salah.';
        $this->load->view('auth/login', $data);
    }

    public function register() {
        if ($this->session->userdata('logged_in')) {
            if ($this->is_api_request()) {
                $this->json_response(['success' => true, 'redirect' => $this->session->userdata('role')]);
                return;
            }
            redirect($this->session->userdata('role'));
        }

        $this->form_validation->set_rules('fullname', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');
        $this->form_validation->set_rules('role', 'Peran', 'required|in_list[admin,user]');

        if ($this->form_validation->run() === FALSE) {
            if ($this->is_api_request()) {
                $this->json_response(['success' => false, 'message' => validation_errors()], 400);
                return;
            }

            $this->load->view('auth/register');
            return;
        }

        $email = $this->input->post('email', TRUE);
        if ($this->User_model->email_exists($email)) {
            if ($this->is_api_request()) {
                $this->json_response(['success' => false, 'message' => 'Email sudah terdaftar.'], 400);
                return;
            }

            $data['error'] = 'Email sudah terdaftar.';
            $this->load->view('auth/register', $data);
            return;
        }

        $userData = [
            'fullname' => $this->input->post('fullname', TRUE),
            'email' => $email,
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role' => $this->input->post('role', TRUE),
        ];

        if ($this->User_model->create_user($userData)) {
            if ($this->is_api_request()) {
                $this->json_response(['success' => true, 'message' => 'Pendaftaran berhasil. Silakan login.']);
                return;
            }

            $this->session->set_flashdata('success', 'Pendaftaran berhasil. Silakan login.');
            redirect('auth');
            return;
        }

        if ($this->is_api_request()) {
            $this->json_response(['success' => false, 'message' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'], 500);
            return;
        }

        $data['error'] = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
        $this->load->view('auth/register', $data);
    }

    public function session() {
        if (!$this->session->userdata('logged_in')) {
            $this->json_response(['logged_in' => false]);
            return;
        }

        $this->json_response([
            'logged_in' => true,
            'user_id' => $this->session->userdata('user_id'),
            'fullname' => $this->session->userdata('fullname'),
            'email' => $this->session->userdata('email'),
            'role' => $this->session->userdata('role'),
        ]);
    }

    public function logout() {
        $this->session->sess_destroy();
        if ($this->is_api_request()) {
            $this->json_response(['success' => true, 'message' => 'Logout berhasil.']);
            return;
        }
        redirect('auth');
    }
}
