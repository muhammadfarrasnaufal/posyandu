<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $title = 'Kelola Pengguna'; ?>
<?php $this->load->view('templates/header'); ?>
<div class="row">
    <div class="col-12 mb-3">
        <h2>Kelola Pengguna</h2>
        <p>Kelola daftar akun admin dan pengguna posyandu.</p>
    </div>
</div>
<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Daftar Pengguna</h5>
                <?php if (empty($users)): ?>
                    <p class="text-muted">Tidak ada pengguna terdaftar.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Peran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= html_escape($user->fullname) ?></td>
                                        <td><?= html_escape($user->email) ?></td>
                                        <td><?= html_escape($user->role) ?></td>
                                        <td>
                                            <?php if ($user->id !== $this->session->userdata('user_id')): ?>
                                                <a href="<?= site_url('admin/delete_user/' . $user->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                                            <?php else: ?>
                                                <span class="text-muted small">Akun aktif</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Tambah Pengguna Baru</h5>
                <?= form_open('admin/create_user') ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="fullname" class="form-control" value="<?= set_value('fullname') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Peran</label>
                        <select name="role" class="form-select" required>
                            <option value="user" <?= set_select('role', 'user', TRUE) ?>>Pengguna</option>
                            <option value="admin" <?= set_select('role', 'admin') ?>>Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Pengguna</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('templates/footer'); ?>
