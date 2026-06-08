<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? $title . ' | Posyandu' : 'Posyandu' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url() ?>">Posyandu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php /** @var mixed $ci */
                $ci =& get_instance();
                /** @var CI_Session $session */
                $session = $ci->session; ?>
                <?php if ($session->userdata('logged_in')) : ?>
                    <?php if ($session->userdata('role') === 'admin') : ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('admin') ?>">Dashboard Admin</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/users') ?>">Kelola Pengguna</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/schedules') ?>">Jadwal Imunisasi</a></li>
                    <?php else : ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('user') ?>">Dashboard Pengguna</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link text-danger" href="<?= site_url('auth/logout') ?>">Logout</a></li>
                <?php else : ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('auth') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('auth/register') ?>">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
