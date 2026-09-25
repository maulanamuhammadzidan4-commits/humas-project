<?php
session_start();

require_once '../backend/connection.php';
require_once 'includes/auth.php';
require_once 'backend/dashboard_data.php';

$page_title = "Dashboard";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Admin Humas SMK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>

<div class="admin-main">
    <?php include 'includes/header.php'; ?>

    <main class="admin-content">

        <!-- Flash Messages -->
        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= $_GET['type'] ?? 'success' ?> flash-alert">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars(urldecode($_GET['msg'])) ?>
        </div>
        <?php endif; ?>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-text" style="position:relative;z-index:1;">
                <h2>Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?>! 👋</h2>
                <p>Berikut ringkasan data sistem Humas SMK hari ini.</p>
            </div>
            <span class="welcome-badge">
                <i class="fa-solid fa-shield-halved" style="margin-right:6px;"></i>
                <?= htmlspecialchars($_SESSION['jabatan'] ?? 'Admin') ?>
            </span>
        </div>

        <!-- Stat Cards -->
        <div class="stat-grid">
            <a href="perusahaan.php" class="stat-card" style="--card-color:#2563eb;--card-bg:#dbeafe; text-decoration:none;">
                <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['perusahaan'] ?></span>
                    <span class="stat-label">Perusahaan Mitra</span>
                </div>
            </a>
            <a href="perusahaan.php" class="stat-card" style="--card-color:#10b981;--card-bg:#d1fae5; text-decoration:none;">
                <div class="stat-icon"><i class="fa-solid fa-handshake"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['mou_aktif'] ?></span>
                    <span class="stat-label">MoU Aktif</span>
                </div>
            </a>
            <a href="lowongan.php" class="stat-card" style="--card-color:#8b5cf6;--card-bg:#ede9fe; text-decoration:none;">
                <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['lowongan'] ?></span>
                    <span class="stat-label">Total Lowongan</span>
                </div>
            </a>
            <a href="lowongan.php" class="stat-card" style="--card-color:#f59e0b;--card-bg:#fef3c7; text-decoration:none;">
                <div class="stat-icon"><i class="fa-solid fa-door-open"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['loker_buka'] ?></span>
                    <span class="stat-label">Loker Dibuka</span>
                </div>
            </a>
            <a href="siswa.php" class="stat-card" style="--card-color:#0ea5e9;--card-bg:#e0f2fe; text-decoration:none;">
                <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['siswa'] ?></span>
                    <span class="stat-label">Total Siswa</span>
                </div>
            </a>
            <a href="pkl.php" class="stat-card" style="--card-color:#f97316;--card-bg:#ffedd5; text-decoration:none;">
                <div class="stat-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['pkl'] ?></span>
                    <span class="stat-label">Penempatan PKL</span>
                </div>
            </a>
            <a href="tracer.php" class="stat-card" style="--card-color:#ec4899;--card-bg:#fce7f3; text-decoration:none;">
                <div class="stat-icon"><i class="fa-solid fa-route"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['tracer'] ?></span>
                    <span class="stat-label">Tracer Study</span>
                </div>
            </a>
            <a href="users.php" class="stat-card" style="--card-color:#64748b;--card-bg:#f1f5f9; text-decoration:none;">
                <div class="stat-icon"><i class="fa-solid fa-users-gear"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['users'] ?></span>
                    <span class="stat-label">User Admin</span>
                </div>
            </a>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">

            <!-- PKL Terbaru -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> PKL Terbaru</span>
                    <a href="pkl.php" class="btn btn-outline btn-sm">Lihat Semua</a>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Perusahaan</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $has_pkl = false;
                        while ($row = mysqli_fetch_assoc($pkl_terbaru)):
                            $has_pkl = true;
                            $badge = match($row['status_penempatan']) {
                                'Disetujui' => 'badge-green',
                                'Selesai'   => 'badge-blue',
                                default     => 'badge-gold',
                            };
                        ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($row['siswa']) ?></td>
                                <td><?= htmlspecialchars($row['perusahaan']) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_mulai'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_selesai'])) ?></td>
                                <td><span class="badge <?= $badge ?>"><?= $row['status_penempatan'] ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if (!$has_pkl): ?>
                            <tr><td colspan="5">
                                <div class="table-empty">
                                    <i class="fa-solid fa-inbox"></i>
                                    <p>Belum ada data PKL.</p>
                                </div>
                            </td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tracer Study Distribution -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-chart-bar"></i> Distribusi Alumni</span>
                    <a href="tracer.php" class="btn btn-outline btn-sm">Detail</a>
                </div>
                <div class="tracer-donut-wrap">
                    <?php
                    $tracer_colors = [
                        'Bekerja'        => ['#10b981','--card-color:#10b981'],
                        'Kuliah'         => ['#2563eb','--card-color:#2563eb'],
                        'Wirausaha'      => ['#f59e0b','--card-color:#f59e0b'],
                        'Mencari Kerja'  => ['#ef4444','--card-color:#ef4444'],
                        'Menikah'  => ['#2a06ad','--card-color:#ef4444'],
                    ];
                    $total_tracer = array_sum($tracer_data);
                    if ($total_tracer === 0):
                    ?>
                    <div class="table-empty" style="padding:2rem;">
                        <i class="fa-solid fa-chart-pie"></i>
                        <p>Belum ada data tracer study.</p>
                    </div>
                    <?php else:
                    foreach ($tracer_colors as $label => [$color, $style]):
                        $val = $tracer_data[$label] ?? 0;
                        $pct = $total_tracer > 0 ? round($val / $total_tracer * 100) : 0;
                    ?>
                    <div class="tracer-item">
                        <span class="tracer-label"><?= $label ?></span>
                        <div class="tracer-bar-wrap">
                            <div class="tracer-bar" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
                        </div>
                        <span class="tracer-count"><?= $val ?></span>
                    </div>
                    <?php endforeach; endif; ?>

                    <?php if ($total_tracer > 0): ?>
                    <div style="border-top:1px solid var(--slate-200);padding-top:.75rem;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:.8rem;color:var(--slate-600);font-weight:600;">Total Responden</span>
                        <span style="font-size:1.1rem;font-weight:800;color:var(--navy);"><?= $total_tracer ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Lowongan Akan Segera Tutup -->
        <?php
        $expiring_rows = [];
        while ($r = mysqli_fetch_assoc($expiring)) $expiring_rows[] = $r;
        if (!empty($expiring_rows)):
        ?>
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="color:#d97706;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Lowongan Segera Tutup (≤ 7 Hari)
                </span>
                <a href="lowongan.php" class="btn btn-outline btn-sm">Kelola</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Posisi</th>
                            <th>Perusahaan</th>
                            <th>Batas Daftar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($expiring_rows as $row): ?>
                        <tr>
                            <td style="font-weight:600;"><?= htmlspecialchars($row['posisi']) ?></td>
                            <td><?= htmlspecialchars($row['perusahaan']) ?></td>
                            <td style="color:var(--red);font-weight:700;"><?= date('d M Y', strtotime($row['batas_daftar'])) ?></td>
                            <td><span class="badge badge-green">Buka</span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
</body>
</html>
