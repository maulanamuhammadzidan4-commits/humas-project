<?php
$page_title = "Dashboard";
require_once "../backend/connection.php";
require_once "components/header.php";

// Query total ringkasan
$count_perusahaan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM perusahaan"))['total'] ?? 0;
$count_loker      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM lowongan_kerja"))['total'] ?? 0;
$count_siswa      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM siswa"))['total'] ?? 0;
$count_pkl        = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penempatan_pkl"))['total'] ?? 0;
$count_tracer     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tracer_study"))['total'] ?? 0;
$count_users      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users"))['total'] ?? 0;
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="p-4 bg-primary bg-gradient text-white rounded-4 shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="fw-bold mb-1"><i class="fa-solid fa-chart-pie me-2"></i>Dashboard Manajemen Humas SMK</h2>
                <p class="mb-0 text-white-50">Kelola seluruh data relasional ERD (Perusahaan, Lowongan, Siswa, PKL, Tracer Study, dan Pengguna).</p>
            </div>
            <div class="d-flex gap-2">
                <a href="form/lowongan-kerja-form.php" class="btn btn-light text-primary fw-semibold">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Loker
                </a>
                <a href="form/perusahaan-form.php" class="btn btn-outline-light">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Mitra
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once "components/alerts.php"; ?>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <!-- Perusahaan -->
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card h-100 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold small">Mitra Industri</span>
                        <h2 class="fw-bold text-dark mt-1 mb-0"><?= number_format($count_perusahaan) ?></h2>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-building"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="perusahaan.php" class="text-decoration-none small fw-semibold text-primary">Lihat Data &rarr;</a>
                    <a href="form/perusahaan-form.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-plus"></i> Tambah</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lowongan Kerja -->
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold small">Lowongan Kerja</span>
                        <h2 class="fw-bold text-dark mt-1 mb-0"><?= number_format($count_loker) ?></h2>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="lowongan_kerja.php" class="text-decoration-none small fw-semibold text-success">Lihat Data &rarr;</a>
                    <a href="form/lowongan-kerja-form.php" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-plus"></i> Tambah</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Siswa -->
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card h-100 border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold small">Total Siswa</span>
                        <h2 class="fw-bold text-dark mt-1 mb-0"><?= number_format($count_siswa) ?></h2>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="siswa.php" class="text-decoration-none small fw-semibold text-info">Lihat Data &rarr;</a>
                    <a href="form/siswa-form.php" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-plus"></i> Tambah</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Penempatan PKL -->
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card h-100 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold small">Penempatan PKL</span>
                        <h2 class="fw-bold text-dark mt-1 mb-0"><?= number_format($count_pkl) ?></h2>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="penempatan_pkl.php" class="text-decoration-none small fw-semibold text-warning">Lihat Data &rarr;</a>
                    <a href="form/penempatan-pkl-form.php" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-plus"></i> Tambah</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracer Study -->
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card h-100 border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold small">Tracer Study</span>
                        <h2 class="fw-bold text-dark mt-1 mb-0"><?= number_format($count_tracer) ?></h2>
                    </div>
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="tracer_study.php" class="text-decoration-none small fw-semibold text-danger">Lihat Data &rarr;</a>
                    <a href="form/tracer-study-form.php" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-plus"></i> Tambah</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengguna / Users -->
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card h-100 border-start border-secondary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold small">Pengguna / Staf</span>
                        <h2 class="fw-bold text-dark mt-1 mb-0"><?= number_format($count_users) ?></h2>
                    </div>
                    <div class="bg-secondary-subtle text-secondary p-3 rounded-circle fs-4">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="users.php" class="text-decoration-none small fw-semibold text-secondary">Lihat Data &rarr;</a>
                    <a href="form/users-form.php" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-plus"></i> Tambah</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loker Terbaru & Mitra Aktif Preview -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-briefcase text-primary me-2"></i>Lowongan Kerja Terbaru</h5>
                <a href="lowongan_kerja.php" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Posisi</th>
                            <th>Perusahaan</th>
                            <th>Batas Daftar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $latest_loker = mysqli_query($koneksi, "SELECT l.*, p.nama AS nama_perusahaan FROM lowongan_kerja l LEFT JOIN perusahaan p ON l.id_perusahaan = p.id ORDER BY l.id DESC LIMIT 5");
                        if (mysqli_num_rows($latest_loker) > 0):
                            while ($row = mysqli_fetch_assoc($latest_loker)):
                        ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($row['posisi']) ?></td>
                                <td><?= htmlspecialchars($row['nama_perusahaan'] ?? '-') ?></td>
                                <td><?= date('d M Y', strtotime($row['batas_daftar'])) ?></td>
                                <td>
                                    <?php if ($row['status_loker'] === 'Buka'): ?>
                                        <span class="badge bg-success">Buka</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tutup</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data lowongan kerja.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-handshake text-info me-2"></i>Status MoU Mitra</h5>
                <a href="perusahaan.php" class="btn btn-sm btn-outline-primary">Kelola Mitra</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Perusahaan</th>
                            <th>Status MoU</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $mitra_list = mysqli_query($koneksi, "SELECT nama, status_mou FROM perusahaan ORDER BY id DESC LIMIT 5");
                        if (mysqli_num_rows($mitra_list) > 0):
                            while ($m = mysqli_fetch_assoc($mitra_list)):
                                $mou_badge = match($m['status_mou']) {
                                    'Aktif' => 'bg-success',
                                    'Kadaluarsa' => 'bg-danger',
                                    default => 'bg-warning text-dark'
                                };
                        ?>
                            <tr>
                                <td class="fw-medium"><?= htmlspecialchars($m['nama']) ?></td>
                                <td><span class="badge <?= $mou_badge ?>"><?= htmlspecialchars($m['status_mou']) ?></span></td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr><td colspan="2" class="text-center text-muted py-4">Belum ada data mitra perusahaan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once "components/footer.php"; ?>
