<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $title = 'Daftar Posyandu'; ?>
<?php $this->load->view('templates/header'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Daftar Akun Posyandu</h4>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?= form_open('auth/register') ?>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Nama Lengkap</label>
                        <input type="text" name="fullname" id="fullname" class="form-control" value="<?= set_value('fullname') ?>" required>
                        <?= form_error('fullname', '<div class="text-danger small">', '</div>') ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email') ?>" required>
                        <?= form_error('email', '<div class="text-danger small">', '</div>') ?>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <?= form_error('password', '<div class="text-danger small">', '</div>') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            <?= form_error('confirm_password', '<div class="text-danger small">', '</div>') ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Peran</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="user" <?= set_select('role', 'user') ?>>Pengguna</option>
                        </select>
                        <?= form_error('role', '<div class="text-danger small">', '</div>') ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Daftar</button>
                    <a class="btn btn-link" href="<?= site_url('auth') ?>">Sudah punya akun? Login</a>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('templates/footer'); ?>
