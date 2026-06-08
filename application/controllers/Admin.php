<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Loader $load
 * @property Posyandu_model $Posyandu_model
 * @property User_model $User_model
 * @property Schedule_model $Schedule_model
 * @property Notification_model $Notification_model
 */
class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        $this->load->model(['Posyandu_model', 'User_model', 'Schedule_model', 'Notification_model']);
        $this->check_login();
    }

    private function check_login() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('auth');
        }
    }

    public function index() {
        $data['title'] = 'Dashboard Admin';
        $data['records'] = $this->Posyandu_model->get_all();
        $data['users'] = $this->User_model->get_non_admin();
        $data['stats'] = [
            'total' => $this->Posyandu_model->count_all(),
            'today' => $this->Posyandu_model->count_today(),
            'recent' => count($this->Posyandu_model->get_recent_records(5)),
            'users' => count($data['users']),
            'upcoming' => $this->Schedule_model->count_upcoming(14),
        ];
        $data['upcoming_schedules'] = $this->Schedule_model->get_upcoming(14, 5);
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        $this->load->view('admin/dashboard', $data);
    }

    public function create() {
        $this->form_validation->set_rules('nama', 'Nama Balita', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('berat_badan', 'Berat Badan', 'required|numeric');
        $this->form_validation->set_rules('tinggi_badan', 'Tinggi Badan', 'required|numeric');
        $this->form_validation->set_rules('tanggal_kunjungan', 'Tanggal Kunjungan', 'required');
        $this->form_validation->set_rules('user_id', 'Pengguna', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin');
            return;
        }

        $record = [
            'user_id' => $this->input->post('user_id', TRUE),
            'nama' => $this->input->post('nama', TRUE),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', TRUE),
            'berat_badan' => $this->input->post('berat_badan', TRUE),
            'tinggi_badan' => $this->input->post('tinggi_badan', TRUE),
            'tanggal_kunjungan' => $this->input->post('tanggal_kunjungan', TRUE),
            'catatan' => $this->input->post('catatan', TRUE),
        ];

        if ($this->Posyandu_model->insert_record($record)) {
            $this->add_notification('Data Posyandu Baru', 'Data posyandu untuk ' . $record['nama'] . ' telah ditambahkan.');
            $this->session->set_flashdata('success', 'Data posyandu berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data posyandu.');
        }

        redirect('admin');
    }

    public function records_json() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            show_error('Akses ditolak.', 403);
            return;
        }

        $records = $this->Posyandu_model->get_all();
        header('Content-Type: application/json');
        echo json_encode($records);
    }

    public function notifications_json() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            show_error('Akses ditolak.', 403);
            return;
        }

        $since_id = intval($this->input->get('since_id'));
        $notifications = $this->Notification_model->get_recent($since_id);
        header('Content-Type: application/json');
        echo json_encode($notifications);
    }

    public function dashboard_json() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            show_error('Akses ditolak.', 403);
            return;
        }

        $records = $this->Posyandu_model->get_recent_records(10);
        $notifications = $this->Notification_model->get_recent();
        $schedules = $this->Schedule_model->get_upcoming(14, 5);
        $users = $this->User_model->get_non_admin();
        $usersPayload = array_map(static function ($user) {
            return [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'email' => $user->email,
            ];
        }, $users);

        $stats = [
            'total' => $this->Posyandu_model->count_all(),
            'today' => $this->Posyandu_model->count_today(),
            'recent' => count($records),
            'users' => count($users),
            'upcoming' => $this->Schedule_model->count_upcoming(14),
        ];

        header('Content-Type: application/json');
        echo json_encode([
            'user' => [
                'fullname' => $this->session->userdata('fullname'),
                'email' => $this->session->userdata('email'),
            ],
            'stats' => $stats,
            'records' => $records,
            'notifications' => $notifications,
            'schedules' => $schedules,
            'users' => $usersPayload,
        ]);
    }

    private function add_notification($title, $body, $type = 'info') {
        $this->Notification_model->create([
            'title' => $title,
            'body' => $body,
            'type' => $type,
        ]);
    }

    public function schedules() {
        $data['title'] = 'Jadwal Imunisasi';
        $data['schedules'] = $this->Schedule_model->get_all();
        $data['users'] = $this->User_model->get_non_admin();
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        $this->load->view('admin/schedules', $data);
    }

    public function create_schedule() {
        $this->form_validation->set_rules('user_id', 'Pengguna', 'required|numeric');
        $this->form_validation->set_rules('child_name', 'Nama Anak', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('vaccine_name', 'Imunisasi', 'required');
        $this->form_validation->set_rules('jadwal', 'Jadwal', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/schedules');
            return;
        }

        $schedule = [
            'user_id' => $this->input->post('user_id', TRUE),
            'child_name' => $this->input->post('child_name', TRUE),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', TRUE),
            'vaccine_name' => $this->input->post('vaccine_name', TRUE),
            'jadwal' => $this->input->post('jadwal', TRUE),
            'status' => $this->input->post('status', TRUE),
            'notes' => $this->input->post('notes', TRUE),
        ];

        if ($this->Schedule_model->insert_schedule($schedule)) {
            $this->add_notification('Jadwal Imunisasi Baru', 'Jadwal imunisasi untuk ' . $schedule['child_name'] . ' telah dibuat.');
            $this->session->set_flashdata('success', 'Jadwal imunisasi berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan jadwal imunisasi.');
        }

        redirect('admin/schedules');
    }

    public function edit_schedule($id) {
        $schedule = $this->Schedule_model->get_by_id($id);
        if (!$schedule) {
            $this->session->set_flashdata('error', 'Jadwal tidak ditemukan.');
            redirect('admin/schedules');
            return;
        }

        $data['title'] = 'Edit Jadwal Imunisasi';
        $data['schedule'] = $schedule;
        $data['users'] = $this->User_model->get_non_admin();
        $data['error'] = $this->session->flashdata('error');
        $this->load->view('admin/edit_schedule', $data);
    }

    public function update_schedule($id) {
        $this->form_validation->set_rules('user_id', 'Pengguna', 'required|numeric');
        $this->form_validation->set_rules('child_name', 'Nama Anak', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('vaccine_name', 'Imunisasi', 'required');
        $this->form_validation->set_rules('jadwal', 'Jadwal', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/edit_schedule/' . $id);
            return;
        }

        $schedule = [
            'user_id' => $this->input->post('user_id', TRUE),
            'child_name' => $this->input->post('child_name', TRUE),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', TRUE),
            'vaccine_name' => $this->input->post('vaccine_name', TRUE),
            'jadwal' => $this->input->post('jadwal', TRUE),
            'status' => $this->input->post('status', TRUE),
            'notes' => $this->input->post('notes', TRUE),
        ];

        if ($this->Schedule_model->update_schedule($id, $schedule)) {
            $this->add_notification('Jadwal Imunisasi Diperbarui', 'Jadwal imunisasi untuk ' . $schedule['child_name'] . ' telah diubah.');
            $this->session->set_flashdata('success', 'Jadwal imunisasi berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui jadwal imunisasi.');
        }

        redirect('admin/schedules');
    }

    public function delete_schedule($id) {
        if ($this->Schedule_model->delete_schedule($id)) {
            $this->add_notification('Jadwal Imunisasi Dihapus', 'Sebuah jadwal imunisasi telah dihapus.');
            $this->session->set_flashdata('success', 'Jadwal imunisasi berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus jadwal imunisasi.');
        }

        redirect('admin/schedules');
    }

    public function export_csv($type) {
        if ($type === 'schedules') {
            $rows = $this->Schedule_model->get_all();
            $filename = 'jadwal_imunisasi_' . date('Ymd_His') . '.csv';
            $headers = ['Nama Anak', 'Jenis Kelamin', 'Imunisasi', 'Jadwal', 'Status', 'Pemilik', 'Catatan'];
        } else {
            $rows = $this->Posyandu_model->get_all();
            $filename = 'posyandu_records_' . date('Ymd_His') . '.csv';
            $headers = ['Nama', 'Jenis Kelamin', 'Tanggal Lahir', 'Berat Badan', 'Tinggi Badan', 'Tanggal Kunjungan', 'Catatan', 'Pemilik'];
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($rows as $row) {
            if ($type === 'schedules') {
                fputcsv($output, [
                    $row->child_name,
                    $row->jenis_kelamin,
                    $row->vaccine_name,
                    $row->jadwal,
                    $row->status,
                    $row->owner_name,
                    $row->notes,
                ]);
            } else {
                fputcsv($output, [
                    $row->nama,
                    $row->jenis_kelamin,
                    $row->tanggal_lahir,
                    $row->berat_badan,
                    $row->tinggi_badan,
                    $row->tanggal_kunjungan,
                    $row->catatan,
                    $row->owner_name,
                ]);
            }
        }

        fclose($output);
        exit;
    }

    public function export_pdf($type) {
        $this->load->library('simple_pdf');
        $pdf = new Simple_pdf();

        if ($type === 'schedules') {
            $rows = $this->Schedule_model->get_all();
            $pdf->Text(40, 40, 'Laporan Jadwal Imunisasi');
            $y = 70;
            foreach ($rows as $row) {
                $pdf->Text(40, $y, sprintf('%s | %s | %s | %s | %s | %s', $row->child_name, $row->vaccine_name, $row->jadwal, $row->status, $row->owner_name, $row->notes));
                $y += 16;
            }
            $filename = 'jadwal_imunisasi_' . date('Ymd_His') . '.pdf';
        } else {
            $rows = $this->Posyandu_model->get_all();
            $pdf->Text(40, 40, 'Laporan Posyandu');
            $y = 70;
            foreach ($rows as $row) {
                $pdf->Text(40, $y, sprintf('%s | %s | %s | %s kg | %s cm | %s | %s', $row->nama, $row->jenis_kelamin, $row->tanggal_kunjungan, $row->berat_badan, $row->tinggi_badan, $row->owner_name, $row->catatan));
                $y += 16;
            }
            $filename = 'posyandu_records_' . date('Ymd_His') . '.pdf';
        }

        $pdf->Output($filename, 'I');
    }

    public function edit($id) {
        $record = $this->Posyandu_model->get_by_id($id);

        if (!$record) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan.');
            redirect('admin');
            return;
        }

        $data['title'] = 'Edit Data Posyandu';
        $data['record'] = $record;
        $data['users'] = $this->User_model->get_non_admin();
        $data['error'] = $this->session->flashdata('error');
        $this->load->view('admin/edit_record', $data);
    }

    public function update($id) {
        $this->form_validation->set_rules('nama', 'Nama Balita', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('berat_badan', 'Berat Badan', 'required|numeric');
        $this->form_validation->set_rules('tinggi_badan', 'Tinggi Badan', 'required|numeric');
        $this->form_validation->set_rules('tanggal_kunjungan', 'Tanggal Kunjungan', 'required');
        $this->form_validation->set_rules('user_id', 'Pengguna', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/edit/' . $id);
            return;
        }

        $record = [
            'user_id' => $this->input->post('user_id', TRUE),
            'nama' => $this->input->post('nama', TRUE),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', TRUE),
            'berat_badan' => $this->input->post('berat_badan', TRUE),
            'tinggi_badan' => $this->input->post('tinggi_badan', TRUE),
            'tanggal_kunjungan' => $this->input->post('tanggal_kunjungan', TRUE),
            'catatan' => $this->input->post('catatan', TRUE),
        ];

        if ($this->Posyandu_model->update_record($id, $record)) {
            $this->add_notification('Data Posyandu Diperbarui', 'Data posyandu untuk ' . $record['nama'] . ' telah diperbarui.');
            $this->session->set_flashdata('success', 'Data posyandu berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data posyandu.');
        }

        redirect('admin');
    }

    public function delete($id) {
        if ($this->Posyandu_model->delete_record($id)) {
            $this->add_notification('Data Posyandu Dihapus', 'Sebuah data posyandu telah dihapus.');
            $this->session->set_flashdata('success', 'Data posyandu berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data posyandu.');
        }

        redirect('admin');
    }

    public function users() {
        $data['title'] = 'Kelola Pengguna';
        $data['users'] = $this->User_model->get_all();
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        $this->load->view('admin/users', $data);
    }

    public function create_user() {
        $this->form_validation->set_rules('fullname', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('role', 'Peran', 'required|in_list[admin,user]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/users');
            return;
        }

        $email = $this->input->post('email', TRUE);
        if ($this->User_model->email_exists($email)) {
            $this->session->set_flashdata('error', 'Email sudah terdaftar.');
            redirect('admin/users');
            return;
        }

        $userData = [
            'fullname' => $this->input->post('fullname', TRUE),
            'email' => $email,
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role' => $this->input->post('role', TRUE),
        ];

        if ($this->User_model->create_user($userData)) {
            $this->session->set_flashdata('success', 'Pengguna baru berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan pengguna.');
        }

        redirect('admin/users');
    }

    public function delete_user($id) {
        if ($this->session->userdata('user_id') == $id) {
            $this->session->set_flashdata('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
            redirect('admin/users');
            return;
        }

        if ($this->User_model->delete_user($id)) {
            $this->session->set_flashdata('success', 'Pengguna berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pengguna.');
        }

        redirect('admin/users');
    }
}
