<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php /** @var object $record */ ?>
<?php /** @var object[] $users */ ?>
<?php $title = 'Edit Data Posyandu'; ?>
<?php $this->load->view('templates/header'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Edit Data Posyandu</h4>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?= form_open('admin/update/' . $record->id) ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Balita</label>
                        <input type="text" name="nama" class="form-control" value="<?= html_escape($record->nama) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">Pilih</option>
                            <option value="Laki-laki" <?= $record->jenis_kelamin === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $record->jenis_kelamin === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pemilik Data</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Pilih pengguna</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user->id ?>" <?= $record->user_id == $user->id ? 'selected' : '' ?>><?= html_escape($user->fullname) ?> (<?= html_escape($user->email) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= html_escape($record->tanggal_lahir) ?>" required>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-6">
                            <label class="form-label">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" class="form-control" value="<?= html_escape($record->berat_badan) ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" class="form-control" value="<?= html_escape($record->tinggi_badan) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kunjungan</label>
                        <input type="date" name="tanggal_kunjungan" class="form-control" value="<?= html_escape($record->tanggal_kunjungan) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="4"><?= html_escape($record->catatan) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="<?= site_url('admin') ?>" class="btn btn-secondary">Kembali</a>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('templates/footer'); ?>
