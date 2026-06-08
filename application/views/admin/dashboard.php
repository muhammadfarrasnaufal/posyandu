<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php /** @var array $stats */ ?>
<?php /** @var array $users */ ?>
<?php $title = 'Dashboard Admin'; ?>
<?php $this->load->view('templates/header'); ?>
<style>
    .admin-hero {
        background: linear-gradient(135deg, #0b69b8 0%, #0d9488 100%);
        color: #ffffff;
        border-radius: 1.25rem;
        box-shadow: 0 24px 60px rgba(13, 40, 65, 0.12);
    }
    .admin-hero h2,
    .admin-hero p,
    .admin-hero .btn {
        color: #ffffff;
    }
    .admin-stats .card {
        border-top-width: 3px;
    }
    .admin-stat-icon {
        width: 3rem;
        height: 3rem;
        display: grid;
        place-items: center;
        border-radius: 0.85rem;
        color: #ffffff;
    }
    .admin-stat-icon.primary { background: #0b69b8; }
    .admin-stat-icon.success { background: #0d9488; }
    .admin-stat-icon.info { background: #22d3ee; }
    .admin-stat-icon.warning { background: #f59e0b; }
    .admin-refresh-banner {
        background: rgba(14, 165, 233, 0.08);
        border-left: 4px solid #0d9488;
    }
    .admin-notification-list .list-group-item {
        border: none;
        border-radius: 0.75rem;
        margin-bottom: 0.5rem;
        background: #f8fafc;
    }
    .admin-action-btn {
        min-width: 140px;
    }
</style>
<div class="row mb-4">
    <div class="col-12">
        <div class="card admin-hero shadow-sm p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="mb-2">Dashboard Admin</h2>
                    <p class="mb-2">Selamat datang, <?= html_escape($this->session->userdata('fullname')) ?>. Kelola data Posyandu, pantau laporan, dan lihat notifikasi penting di sini.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= site_url('admin/schedules') ?>" class="btn btn-outline-light admin-action-btn">Kelola Jadwal</a>
                        <a href="<?= site_url('admin/export_csv/records') ?>" class="btn btn-light text-dark admin-action-btn">Cetak CSV</a>
                        <a href="<?= site_url('admin/export_pdf/records') ?>" class="btn btn-light text-dark admin-action-btn">Cetak PDF</a>
                    </div>
                </div>
                <div class="text-end">
                    <div class="badge bg-white text-dark px-3 py-2 shadow-sm">Admin Posyandu</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>
<div class="row mb-4 admin-stats">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="admin-stat-icon primary">📊</div>
                <div>
                    <h6 class="card-title text-uppercase text-muted mb-1">Total Data</h6>
                    <h3><?= html_escape($stats['total']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="admin-stat-icon success">🗓</div>
                <div>
                    <h6 class="card-title text-uppercase text-muted mb-1">Data Hari Ini</h6>
                    <h3><?= html_escape($stats['today']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="admin-stat-icon info">⏰</div>
                <div>
                    <h6 class="card-title text-uppercase text-muted mb-1">Jadwal 2 Minggu</h6>
                    <h3><?= html_escape($stats['upcoming']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="admin-stat-icon warning">👥</div>
                <div>
                    <h6 class="card-title text-uppercase text-muted mb-1">Pengguna Terdaftar</h6>
                    <h3><?= html_escape($stats['users']) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4 g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Notifikasi Terbaru</h5>
                    <a href="<?= site_url('admin') ?>" class="small text-decoration-none">Muat ulang</a>
                </div>
                <ul id="notification-list" class="list-group admin-notification-list list-group-flush small"></ul>
                <p class="mt-3 text-muted small">Notifikasi diperbarui otomatis setiap 12 detik.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Pengguna Non-Admin</h5>
                    <span class="badge bg-secondary"><?= html_escape($stats['users']) ?></span>
                </div>
                <?php if (empty($users)): ?>
                    <p class="text-muted">Tidak ada pengguna non-admin.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush small">
                        <?php foreach (array_slice($users, 0, 5) as $user): ?>
                            <li class="list-group-item py-2">
                                <strong><?= html_escape($user->fullname) ?></strong><br>
                                <small class="text-muted"><?= html_escape($user->email) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <a href="<?= site_url('admin/schedules') ?>" class="btn btn-sm btn-outline-primary mt-3">Lihat Jadwal dan Pengguna</a>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="card-title mb-0">Jadwal Imunisasi Mendatang</h5>
                        <p class="text-muted small mb-0">Menampilkan 5 jadwal terdekat dalam 14 hari ke depan.</p>
                    </div>
                    <a href="<?= site_url('admin/schedules') ?>" class="small">Kelola jadwal</a>
                </div>
                <?php if (empty($upcoming_schedules)): ?>
                    <p class="text-muted mb-0">Tidak ada jadwal imunisasi dalam 14 hari ke depan.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Anak</th>
                                    <th>Vaksin</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming_schedules as $schedule): ?>
                                    <tr>
                                        <td><?= html_escape($schedule->child_name) ?></td>
                                        <td><?= html_escape($schedule->vaccine_name) ?></td>
                                        <td><?= html_escape($schedule->jadwal) ?></td>
                                        <td><?= html_escape($schedule->status) ?></td>
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
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Tambah Data Posyandu</h5>
                <?= form_open('admin/create') ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Balita</label>
                        <input type="text" name="nama" class="form-control" required>
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
                    <div class="mb-3 row">
                        <div class="col-6">
                            <label class="form-label">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pemilik Data</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Pilih pengguna</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user->id ?>"><?= html_escape($user->fullname) ?> (<?= html_escape($user->email) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kunjungan</label>
                        <input type="date" name="tanggal_kunjungan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Data Posyandu Terbaru</h5>
                <?php if (empty($records)): ?>
                    <p class="text-muted">Belum ada data posyandu. Tambahkan data menggunakan form.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>JK</th>
                                    <th>Pemilik</th>
                                    <th>Tgl. Kunjungan</th>
                                    <th>Berat</th>
                                    <th>Tinggi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="admin-records-body">
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td><?= html_escape($record->nama) ?></td>
                                        <td><?= html_escape($record->jenis_kelamin) ?></td>
                                        <td><?= html_escape($record->owner_name ?: 'Umum') ?></td>
                                        <td><?= html_escape($record->tanggal_kunjungan) ?></td>
                                        <td><?= html_escape($record->berat_badan) ?> kg</td>
                                        <td><?= html_escape($record->tinggi_badan) ?> cm</td>
                                        <td>
                                            <a href="<?= site_url('admin/edit/' . $record->id) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="<?= site_url('admin/delete/' . $record->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
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
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-light small admin-refresh-banner">
            Data akan diperbarui otomatis setiap 12 detik. Terakhir refresh: <span id="admin-refresh-time">-</span>
        </div>
    </div>
</div>
<script>
    const adminRecordsBody = document.getElementById('admin-records-body');
    const adminRefreshTime = document.getElementById('admin-refresh-time');

    function updateRefreshTime() {
        adminRefreshTime.textContent = new Date().toLocaleTimeString();
    }

    updateRefreshTime();

    async function refreshAdminRecords() {
        try {
            const response = await fetch('<?= site_url('admin/records_json') ?>');
            const data = await response.json();
            adminRecordsBody.innerHTML = '';
            data.forEach(record => {
                const row = document.createElement('tr');

                const nameCell = document.createElement('td');
                nameCell.textContent = record.nama;
                row.appendChild(nameCell);

                const genderCell = document.createElement('td');
                genderCell.textContent = record.jenis_kelamin;
                row.appendChild(genderCell);

                const ownerCell = document.createElement('td');
                ownerCell.textContent = record.owner_name || 'Umum';
                row.appendChild(ownerCell);

                const visitCell = document.createElement('td');
                visitCell.textContent = record.tanggal_kunjungan;
                row.appendChild(visitCell);

                const weightCell = document.createElement('td');
                weightCell.textContent = `${record.berat_badan} kg`;
                row.appendChild(weightCell);

                const heightCell = document.createElement('td');
                heightCell.textContent = `${record.tinggi_badan} cm`;
                row.appendChild(heightCell);

                const actionCell = document.createElement('td');
                actionCell.innerHTML = `
                    <a href="<?= site_url('admin/edit/') ?>${record.id}" class="btn btn-sm btn-warning">Edit</a>
                    <a href="<?= site_url('admin/delete/') ?>${record.id}" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                `;
                row.appendChild(actionCell);

                adminRecordsBody.appendChild(row);
            });
            updateRefreshTime();
        } catch (error) {
            console.error('Gagal memperbarui data:', error);
            updateRefreshTime();
        }
    }
    refreshAdminRecords();
    setInterval(refreshAdminRecords, 12000);

    let latestNotificationId = 0;
    const notificationList = document.getElementById('notification-list');

    async function refreshNotifications() {
        try {
            const response = await fetch('<?= site_url('admin/notifications_json') ?>?since_id=' + latestNotificationId);
            const data = await response.json();
            data.forEach(notification => {
                latestNotificationId = Math.max(latestNotificationId, notification.id);
                const item = document.createElement('li');
                item.className = 'list-group-item py-2';
                item.textContent = `${notification.title} - ${notification.body}`;
                notificationList.prepend(item);
            });
        } catch (error) {
            console.error('Gagal memuat notifikasi:', error);
        }
    }

    refreshNotifications();
    setInterval(refreshNotifications, 12000);
</script>
<?php $this->load->view('templates/footer'); ?>
