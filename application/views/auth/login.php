<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $title = 'Login Posyandu'; ?>
<?php $this->load->view('templates/header'); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Login Posyandu</h4>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?= form_open('auth/login') ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email') ?>" required>
                        <?= form_error('email', '<div class="text-danger small">', '</div>') ?>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <?= form_error('password', '<div class="text-danger small">', '</div>') ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Masuk</button>
                <?= form_close() ?>
                <hr>
                <p class="small text-muted">Admin: admin@posyandu.local / admin123</p>
                <p class="small text-muted">Pengguna: user@posyandu.local / user123</p>
                <p><a href="<?= site_url('auth/register') ?>">Belum punya akun? Daftar di sini.</a></p>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('templates/footer'); ?>
