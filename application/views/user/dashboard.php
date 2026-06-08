<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php /** @var string $fullname */ ?>
<?php /** @var string $email */ ?>
<?php /** @var string|null $avatar_url */ ?>
<?php /** @var string|null $success */ ?>
<?php /** @var string|null $error */ ?>
<?php /** @var array $stats */ ?>
<?php /** @var object[] $records */ ?>
<?php /** @var object[] $upcoming_schedules */ ?>
<?php /** @var object|null $next_schedule */ ?>
<?php $title = 'Dashboard Pengguna'; ?>
<?php $this->load->view('templates/header'); ?>
<style>
    .hospital-hero {
        background: linear-gradient(135deg, #0a5275 0%, #0f766e 100%);
        color: #f8fafc;
        border-radius: 1.25rem;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.14);
    }
    .hospital-hero h1,
    .hospital-hero p {
        color: #f8fafc;
    }
    .hospital-card {
        border-top: 4px solid #0f766e;
    }
    .hospital-card-2 {
        border-top: 4px solid #0a5275;
    }
    .dashboard-sidebar {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        padding: 1.35rem;
    }
    .dashboard-sidebar h5 {
        margin-bottom: 1rem;
    }
    .sidebar-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.95rem;
        margin-bottom: 0.8rem;
        color: #0f172a;
        text-decoration: none;
        background: #f8fafc;
        transition: background 0.2s ease;
    }
    .sidebar-link:hover {
        background: #e0f2fe;
        text-decoration: none;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.65rem;
        font-size: 0.8rem;
        border-radius: 999px;
    }
    .status-pill.info {
        background: #bfdbfe;
        color: #1e40af;
    }
    .status-pill.success {
        background: #bbf7d0;
        color: #166534;
    }
    .status-pill.warning {
        background: #fde047;
        color: #78350f;
    }
    .hospital-icon {
        width: 3rem;
        height: 3rem;
        display: grid;
        place-items: center;
        background: rgba(15, 118, 110, 0.12);
        border-radius: 1rem;
        font-size: 1.5rem;
    }
    .hospital-table thead {
        background-color: #dbeafe;
    }
    .hospital-table tbody tr:hover {
        background-color: rgba(14, 165, 233, 0.08);
    }
    .hospital-badge {
        background-color: #0f766e;
        color: #ffffff;
    }
    .hospital-tips li {
        margin-bottom: 0.65rem;
    }
    .refresh-banner {
        background: rgba(14, 165, 233, 0.08);
        border-left: 4px solid #0f766e;
    }
</style>
<div class="row mb-4">
    <div class="col-12">
        <div class="hospital-hero p-4">
            <h1 class="mb-2">Halo, <?= html_escape($fullname) ?>!</h1>
            <p class="mb-0">Selamat datang di portal Posyandu Anda. Pantau perkembangan anak dan jadwal imunisasi lebih mudah di sini.</p>
        </div>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm hospital-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="hospital-icon">🏥</div>
                <div>
                    <h6 class="mb-1">Fasilitas Posyandu</h6>
                    <p class="mb-0 small text-muted">Informasi layanan kesehatan anak.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm hospital-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="hospital-icon">📅</div>
                <div>
                    <h6 class="mb-1">Jadwal Imunisasi</h6>
                    <p class="mb-0 small text-muted">Lihat jadwal imunisasi terdekat.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm hospital-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="hospital-icon">📊</div>
                <div>
                    <h6 class="mb-1">Rekam Kesehatan</h6>
                    <p class="mb-0 small text-muted">Pantau perkembangan balita secara cepat.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 mb-4">
        <aside class="dashboard-sidebar shadow-sm">
            <h5>Portal Posyandu</h5>
            <a href="<?= site_url('user') ?>" class="sidebar-link">
                <span>Dashboard Utama</span>
                <span>→</span>
            </a>
            <a href="#jadwal" class="sidebar-link">
                <span>Jadwal Imunisasi</span>
                <span>→</span>
            </a>
            <a href="#rekam" class="sidebar-link">
                <span>Rekam Kesehatan</span>
                <span>→</span>
            </a>
            <a href="mailto:posyandu@domain.local" class="sidebar-link">
                <span>Hubungi Petugas</span>
                <span>✉</span>
            </a>
            <a href="javascript:window.print()" class="sidebar-link">
                <span>Cetak Laporan</span>
                <span>🖨</span>
            </a>
            <div class="mt-4 p-3 bg-light rounded-3">
                <h6 class="mb-2">Kontak Cepat</h6>
                <p class="small mb-1">Telp: <a href="tel:+6285695034937">+62 856-9503-4937</a></p>
                <p class="small mb-0">Email: <a href="mailto:posyandu@domain.local">posyandu@domain.local</a></p>
            </div>
        </aside>
    </div>
    <div class="col-lg-9">
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-uppercase text-muted">Total Rekam</h6>
                <h2 class="display-6 mb-0"><?= html_escape($stats['total']) ?></h2>
                <small class="text-muted">Data kesehatan balita</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100 border-info">
            <div class="card-body">
                <h6 class="text-uppercase text-muted">Terakhir Diperbarui</h6>
                <h2 class="display-6 mb-0"><?= $stats['last_updated'] ? html_escape($stats['last_updated']) : '-' ?></h2>
                <small class="text-muted">Waktu update catatan terakhir</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100 border-success">
            <div class="card-body">
                <h6 class="text-uppercase text-muted">Jadwal Berikutnya</h6>
                <h2 class="display-6 mb-0"><?= $stats['next_date'] ? html_escape($stats['next_date']) : '-' ?></h2>
                <small class="text-muted">Imunisasi terdekat</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100 border-warning">
            <div class="card-body">
                <h6 class="text-uppercase text-muted">Jadwal Mendatang</h6>
                <h2 class="display-6 mb-0"><?= html_escape($stats['upcoming_count']) ?></h2>
                <small class="text-muted">Imunisasi yang terjadwal</small>
            </div>
        </div>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card shadow-sm h-100 hospital-card">
            <div class="card-body">
                <h5 class="card-title">Profil Anda</h5>
                <?php if (!empty($avatar_url)): ?>
                    <div class="mb-3 text-center">
                        <img src="<?= html_escape($avatar_url) ?>" alt="Avatar" class="img-fluid rounded-circle" style="width: 96px; height: 96px; object-fit: cover;" />
                    </div>
                <?php else: ?>
                    <div class="mb-3 text-center">
                        <div style="width: 96px; height: 96px; display: grid; place-items: center; border-radius: 999px; background: #e2e8f0; color: #0f172a; font-weight: 700;">A</div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success small"><?= html_escape($success) ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger small"><?= html_escape($error) ?></div>
                <?php endif; ?>
                <?= form_open_multipart('auth/update_profile') ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="fullname" class="form-control" value="<?= html_escape($fullname) ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= html_escape($email) ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*" />
                    </div>
                    <?php if (!empty($avatar_url)): ?>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="removeAvatar" />
                            <label class="form-check-label" for="removeAvatar">Hapus foto profil saat ini</label>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary w-100">Simpan Perubahan Profil</button>
                <?= form_close() ?>
                <hr>
                <h6 class="mb-3">Tips Kesehatan</h6>
                <ul class="list-unstyled small mb-0 hospital-tips">
                    <li>• Pastikan imunisasi tepat waktu.</li>
                    <li>• Catat berat dan tinggi setiap kunjungan.</li>
                    <li>• Berikan makanan bergizi sesuai usia.</li>
                    <li>• Konsultasikan bila ada keluhan kesehatan.</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8" id="jadwal">
        <div class="card shadow-sm h-100 hospital-card-2">
            <div class="card-body">
                <h5 class="card-title">Jadwal Imunisasi Mendatang</h5>
                <?php if (empty($upcoming_schedules)): ?>
                    <p class="text-muted mb-0">Belum ada jadwal imunisasi. Hubungi petugas Posyandu untuk penjadwalan.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($upcoming_schedules as $schedule): ?>
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= html_escape($schedule->child_name) ?></strong><br>
                                    <small class="text-muted">Imunisasi: <?= html_escape($schedule->vaccine_name) ?></small>
                                </div>
                                <span class="badge hospital-badge"><?= html_escape($schedule->jadwal) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4" id="pasien-cards">
    <div class="col-12">
        <h5 class="mb-3">Kartu Pasien Anak</h5>
        <div class="row g-3">
            <?php if (empty($upcoming_schedules)): ?>
                <div class="col-12">
                    <div class="alert alert-secondary">Belum ada informasi pasien dengan jadwal imunisasi.</div>
                </div>
            <?php else: ?>
                <?php foreach ($upcoming_schedules as $schedule): ?>
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1"><?= html_escape($schedule->child_name) ?></h6>
                                        <p class="small text-muted mb-1"><?= html_escape($schedule->vaccine_name) ?> • <?= html_escape($schedule->jadwal) ?></p>
                                    </div>
                                    <span class="status-pill <?= $schedule->status === 'Menunggu' ? 'warning' : ($schedule->status === 'Selesai' ? 'success' : 'info') ?>">
                                        <?= html_escape($schedule->status) ?>
                                    </span>
                                </div>
                                <p class="small mb-1"><strong>Orang Tua:</strong> <?= html_escape($schedule->owner_name ?? $email) ?></p>
                                <p class="small mb-0"><?= html_escape($schedule->notes ?: 'Tidak ada catatan tambahan.') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="row mb-4" id="rekam">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Riwayat Data Posyandu</h5>
                <?php if (empty($records)): ?>
                    <div class="alert alert-warning">Belum ada catatan posyandu. Silakan tunggu agar petugas menambahkan hasil pemeriksaan.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle hospital-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>JK</th>
                                    <th>Tgl. Kunjungan</th>
                                    <th>Berat</th>
                                    <th>Tinggi</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="user-records-body">
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td><?= html_escape($record->nama) ?></td>
                                        <td><?= html_escape($record->jenis_kelamin) ?></td>
                                        <td><?= html_escape($record->tanggal_kunjungan) ?></td>
                                        <td><?= html_escape($record->berat_badan) ?> kg</td>
                                        <td><?= html_escape($record->tinggi_badan) ?> cm</td>
                                        <td><?= html_escape($record->catatan) ?></td>
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
<div class="row mb-3">
    <div class="col-12">
        <div class="alert refresh-banner small mb-0">
            Data akan diperbarui otomatis setiap 12 detik. Terakhir refresh: <span id="user-refresh-time"><?= date('H:i:s') ?></span>
        </div>
    </div>
</div>
<script>
    const userRecordsBody = document.getElementById('user-records-body');
    const userRefreshTime = document.getElementById('user-refresh-time');

    function updateRefreshTime() {
        if (userRefreshTime) {
            userRefreshTime.textContent = new Date().toLocaleTimeString();
        }
    }

    async function refreshUserRecords() {
        if (!userRecordsBody) {
            updateRefreshTime();
            return;
        }

        try {
            const response = await fetch('<?= site_url('user/records_json') ?>');
            const data = await response.json();
            userRecordsBody.innerHTML = '';
            data.forEach(record => {
                const row = document.createElement('tr');

                const nameCell = document.createElement('td');
                nameCell.textContent = record.nama;
                row.appendChild(nameCell);

                const genderCell = document.createElement('td');
                genderCell.textContent = record.jenis_kelamin;
                row.appendChild(genderCell);

                const visitCell = document.createElement('td');
                visitCell.textContent = record.tanggal_kunjungan;
                row.appendChild(visitCell);

                const weightCell = document.createElement('td');
                weightCell.textContent = `${record.berat_badan} kg`;
                row.appendChild(weightCell);

                const heightCell = document.createElement('td');
                heightCell.textContent = `${record.tinggi_badan} cm`;
                row.appendChild(heightCell);

                const noteCell = document.createElement('td');
                noteCell.textContent = record.catatan || '-';
                row.appendChild(noteCell);

                userRecordsBody.appendChild(row);
            });
            updateRefreshTime();
        } catch (error) {
            console.error('Gagal memperbarui data:', error);
            updateRefreshTime();
        }
    }

    updateRefreshTime();
    refreshUserRecords();
    setInterval(refreshUserRecords, 12000);
</script>
    </div>
</div>
<?php $this->load->view('templates/footer'); ?>
