<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Loader $load
 * @property CI_Upload $upload
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

    private function get_avatar_url(?string $avatar = null) {
        if (empty($avatar)) {
            return null;
        }

        return base_url($avatar);
    }

    private function upload_avatar() {
        if (empty($_FILES['avatar']['name'])) {
            return null;
        }

        $uploadPath = FCPATH . 'uploads/avatars/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
            @file_put_contents($uploadPath . 'index.html', '');
        }

        $config = [
            'upload_path' => $uploadPath,
            'allowed_types' => 'gif|jpg|jpeg|png',
            'max_size' => 2048,
            'file_name' => 'avatar_' . uniqid() . '_' . time(),
            'overwrite' => false,
        ];

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('avatar')) {
            return ['error' => $this->upload->display_errors('', '')];
        }

        $uploadData = $this->upload->data();
        return 'uploads/avatars/' . $uploadData['file_name'];
    }

    private function delete_avatar_file(?string $avatarPath) {
        if (empty($avatarPath)) {
            return;
        }

        $filePath = FCPATH . $avatarPath;
        if (is_file($filePath)) {
            @unlink($filePath);
        }
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

        $avatarPath = null;
        $avatarUploadResult = $this->upload_avatar();
        if (is_array($avatarUploadResult) && isset($avatarUploadResult['error'])) {
            if ($this->is_api_request()) {
                $this->json_response(['success' => false, 'message' => $avatarUploadResult['error']], 400);
                return;
            }

            $data['error'] = $avatarUploadResult['error'];
            $this->load->view('auth/register', $data);
            return;
        }

        if (is_string($avatarUploadResult)) {
            $avatarPath = $avatarUploadResult;
        }

        $userData = [
            'fullname' => $this->input->post('fullname', TRUE),
            'email' => $email,
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role' => $this->input->post('role', TRUE),
            'avatar' => $avatarPath,
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

        $user = $this->User_model->get_by_id($this->session->userdata('user_id'));
        if (!$user) {
            $this->session->sess_destroy();
            $this->json_response(['logged_in' => false]);
            return;
        }

        $this->json_response([
            'logged_in' => true,
            'user_id' => $user->id,
            'fullname' => $user->fullname,
            'email' => $user->email,
            'role' => $user->role,
            'avatar_url' => $this->get_avatar_url($user->avatar),
        ]);
    }

    public function profile() {
        if (!$this->session->userdata('logged_in')) {
            show_error('Akses ditolak.', 403);
            return;
        }

        $user = $this->User_model->get_by_id($this->session->userdata('user_id'));
        if (!$user) {
            $this->json_response(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
            return;
        }

        $this->json_response([
            'success' => true,
            'profile' => [
                'fullname' => $user->fullname,
                'email' => $user->email,
                'role' => $user->role,
                'avatar_url' => $this->get_avatar_url($user->avatar),
            ],
        ]);
    }

    public function update_profile() {
        $isApiRequest = $this->is_api_request();

        if (!$this->session->userdata('logged_in')) {
            if ($isApiRequest) {
                $this->json_response(['success' => false, 'message' => 'Akses ditolak.'], 403);
                return;
            }
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('auth');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($user_id);
        if (!$user) {
            if ($isApiRequest) {
                $this->json_response(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
                return;
            }
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
            redirect('user');
            return;
        }

        $fullname = $this->input->post('fullname', TRUE);
        $email = $this->input->post('email', TRUE);
        $removeAvatar = $this->input->post('remove_avatar', TRUE) === '1';

        $this->form_validation->set_data([
            'fullname' => $fullname,
            'email' => $email,
        ]);
        $this->form_validation->set_rules('fullname', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() === FALSE) {
            $errorMessage = strip_tags(validation_errors());
            if ($isApiRequest) {
                $this->json_response(['success' => false, 'message' => $errorMessage], 400);
                return;
            }
            $this->session->set_flashdata('error', $errorMessage);
            redirect('user');
            return;
        }

        if ($this->User_model->email_exists_except($email, $user_id)) {
            if ($isApiRequest) {
                $this->json_response(['success' => false, 'message' => 'Email sudah terdaftar oleh pengguna lain.'], 400);
                return;
            }
            $this->session->set_flashdata('error', 'Email sudah terdaftar oleh pengguna lain.');
            redirect('user');
            return;
        }

        $updateData = [
            'fullname' => $fullname,
            'email' => $email,
        ];

        $avatarUploadResult = $this->upload_avatar();
        if (is_array($avatarUploadResult) && isset($avatarUploadResult['error'])) {
            if ($isApiRequest) {
                $this->json_response(['success' => false, 'message' => $avatarUploadResult['error']], 400);
                return;
            }
            $this->session->set_flashdata('error', $avatarUploadResult['error']);
            redirect('user');
            return;
        }

        if ($removeAvatar && !empty($user->avatar)) {
            $this->delete_avatar_file($user->avatar);
            $updateData['avatar'] = null;
        }

        if (is_string($avatarUploadResult)) {
            if (!empty($user->avatar)) {
                $this->delete_avatar_file($user->avatar);
            }
            $updateData['avatar'] = $avatarUploadResult;
        }

        if ($this->User_model->update_user($user_id, $updateData)) {
            $updatedUser = $this->User_model->get_by_id($user_id);
            $this->session->set_userdata([
                'fullname' => $updatedUser->fullname,
                'email' => $updatedUser->email,
            ]);

            if ($isApiRequest) {
                $this->json_response([
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui.',
                    'profile' => [
                        'fullname' => $updatedUser->fullname,
                        'email' => $updatedUser->email,
                        'avatar_url' => $this->get_avatar_url($updatedUser->avatar),
                    ],
                ]);
                return;
            }

            $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
            redirect('user');
            return;
        }

        if ($isApiRequest) {
            $this->json_response(['success' => false, 'message' => 'Gagal memperbarui profil.'], 500);
            return;
        }

        $this->session->set_flashdata('error', 'Gagal memperbarui profil.');
        redirect('user');
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
