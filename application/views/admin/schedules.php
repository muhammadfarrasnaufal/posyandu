<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php /** @var object[] $users */ ?>
<?php /** @var object[] $schedules */ ?>
<?php $title = 'Jadwal Imunisasi'; ?>
<?php $this->load->view('templates/header'); ?>
<div class="row">
    <div class="col-12 mb-3">
        <h2>Jadwal Imunisasi</h2>
        <p>Kelola jadwal imunisasi anak untuk pengguna.</p>
    </div>
</div>
<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Tambah Jadwal Imunisasi</h5>
                <?= form_open('admin/create_schedule') ?>
                    <div class="mb-3">
                        <label class="form-label">Pemilik</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Pilih pengguna</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user->id ?>"><?= html_escape($user->fullname) ?> (<?= html_escape($user->email) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Anak</label>
                        <input type="text" name="child_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">Pilih</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imunisasi</label>
                        <input type="text" name="vaccine_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jadwal</label>
                        <input type="date" name="jadwal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Terjadwal">Terjadwal</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Ekspor Laporan</h5>
                <p>Unduh data sebagai PDF atau CSV untuk evaluasi.</p>
                <a href="<?= site_url('admin/export_csv/records') ?>" class="btn btn-outline-secondary mb-2">Ekspor Posyandu CSV</a>
                <a href="<?= site_url('admin/export_pdf/records') ?>" class="btn btn-outline-secondary mb-2">Ekspor Posyandu PDF</a>
                <a href="<?= site_url('admin/export_csv/schedules') ?>" class="btn btn-outline-secondary mb-2">Ekspor Jadwal CSV</a>
                <a href="<?= site_url('admin/export_pdf/schedules') ?>" class="btn btn-outline-secondary">Ekspor Jadwal PDF</a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Daftar Jadwal Imunisasi</h5>
                <?php if (empty($schedules)): ?>
                    <p class="text-muted">Belum ada jadwal imunisasi.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Anak</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Imunisasi</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th>Pemilik</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $schedule): ?>
                                    <tr>
                                        <td><?= html_escape($schedule->child_name) ?></td>
                                        <td><?= html_escape($schedule->jenis_kelamin) ?></td>
                                        <td><?= html_escape($schedule->vaccine_name) ?></td>
                                        <td><?= html_escape($schedule->jadwal) ?></td>
                                        <td><?= html_escape($schedule->status) ?></td>
                                        <td><?= html_escape($schedule->owner_name ?: 'Umum') ?></td>
                                        <td>
                                            <a href="<?= site_url('admin/edit_schedule/' . $schedule->id) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="<?= site_url('admin/delete_schedule/' . $schedule->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
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
</div>
<?php $this->load->view('templates/footer'); ?>
