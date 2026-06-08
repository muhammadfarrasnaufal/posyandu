<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Loader $load
 * @property Posyandu_model $Posyandu_model
 * @property Schedule_model $Schedule_model
 * @property User_model $User_model
 */
class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session']);
        $this->load->helper(['url']);
        $this->load->model(['Posyandu_model', 'Schedule_model', 'User_model']);
        $this->check_login();
    }

    private function check_login() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'user') {
            redirect('auth');
        }
    }

    public function index() {
        $userId = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($userId);

        $data['title'] = 'Dashboard Pengguna';
        $data['fullname'] = $this->session->userdata('fullname');
        $data['email'] = $this->session->userdata('email');
        $data['avatar_url'] = !empty($user->avatar) ? base_url($user->avatar) : null;
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        $data['records'] = $this->Posyandu_model->get_by_user($userId);
        $data['upcoming_schedules'] = $this->Schedule_model->get_by_user($userId);
        $data['next_schedule'] = null;
        $today = date('Y-m-d');
        foreach ($data['upcoming_schedules'] as $schedule) {
            if ($schedule->jadwal >= $today) {
                $data['next_schedule'] = $schedule;
                break;
            }
        }
        $data['stats'] = [
            'total' => count($data['records']),
            'last_updated' => !empty($data['records']) ? $data['records'][0]->updated_at : null,
            'upcoming_count' => count($data['upcoming_schedules']),
            'next_date' => $data['next_schedule'] ? $data['next_schedule']->jadwal : null,
        ];
        $this->load->view('user/dashboard', $data);
    }

    public function records_json() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'user') {
            show_error('Akses ditolak.', 403);
            return;
        }

        $records = $this->Posyandu_model->get_by_user($this->session->userdata('user_id'));
        header('Content-Type: application/json');
        echo json_encode($records);
    }

    public function dashboard_json() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'user') {
            show_error('Akses ditolak.', 403);
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $records = $this->Posyandu_model->get_by_user($user_id);
        $upcoming_schedules = $this->Schedule_model->get_by_user($user_id);
        $next_schedule = null;
        $today = date('Y-m-d');

        foreach ($upcoming_schedules as $schedule) {
            if ($schedule->jadwal >= $today) {
                $next_schedule = $schedule;
                break;
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'user' => [
                'fullname' => $this->session->userdata('fullname'),
                'email' => $this->session->userdata('email'),
            ],
            'stats' => [
                'total' => count($records),
                'upcoming_count' => count($upcoming_schedules),
                'next_date' => $next_schedule ? $next_schedule->jadwal : null,
                'last_updated' => !empty($records) ? $records[0]->updated_at : null,
            ],
            'records' => $records,
            'upcoming_schedules' => $upcoming_schedules,
            'next_schedule' => $next_schedule,
        ]);
    }
}
