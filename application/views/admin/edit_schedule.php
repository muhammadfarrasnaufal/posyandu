<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php /** @var object $schedule */ ?>
<?php /** @var object[] $users */ ?>
<?php $title = 'Edit Jadwal Imunisasi'; ?>
<?php $this->load->view('templates/header'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Edit Jadwal Imunisasi</h4>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?= form_open('admin/update_schedule/' . $schedule->id) ?>
                    <div class="mb-3">
                        <label class="form-label">Pemilik</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Pilih pengguna</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user->id ?>" <?= $schedule->user_id == $user->id ? 'selected' : '' ?>><?= html_escape($user->fullname) ?> (<?= html_escape($user->email) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Anak</label>
                        <input type="text" name="child_name" class="form-control" value="<?= html_escape($schedule->child_name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="Laki-laki" <?= $schedule->jenis_kelamin === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $schedule->jenis_kelamin === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= html_escape($schedule->tanggal_lahir) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imunisasi</label>
                        <input type="text" name="vaccine_name" class="form-control" value="<?= html_escape($schedule->vaccine_name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jadwal</label>
                        <input type="date" name="jadwal" class="form-control" value="<?= html_escape($schedule->jadwal) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Terjadwal" <?= $schedule->status === 'Terjadwal' ? 'selected' : '' ?>>Terjadwal</option>
                            <option value="Selesai" <?= $schedule->status === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="4"><?= html_escape($schedule->notes) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="<?= site_url('admin/schedules') ?>" class="btn btn-secondary">Kembali</a>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('templates/footer'); ?>
