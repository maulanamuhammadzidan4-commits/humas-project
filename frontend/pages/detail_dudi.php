<?php
session_start();
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';

$id = (int) ($_GET['id'] ?? 0);
$berita = getBeritaById($koneksi, $id);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $berita ? htmlspecialchars($berita['judul']) : 'Berita Tidak Ditemukan'; ?> | Humas SMK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <?php if ($berita): ?>
        <header class="detail-header">
            <div class="detail-header-content">
                <a href="../index.php#berita" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali ke Berita</a>
                <div class="detail-header-title">
                    <div class="detail-header-icon"><i class="fa-solid fa-newspaper"></i></div>
                    <div class="detail-header-text">
                        <span class="section-tag detail-category"><?= htmlspecialchars($berita['kategori'] ?? 'BERITA'); ?></span>
                        <h1><?= htmlspecialchars($berita['judul'] ?? 'Berita'); ?></h1>
                        <p><i class="fa-regular fa-calendar-check"></i> <?= formatTanggalIndo($berita['tanggal'] ?? ''); ?> &bull; DIPUBLIKASIKAN OLEH HUMAS SMK</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="detail-container">
            <div class="detail-grid">
                <div class="detail-main">
                    <?php if (!empty($berita['gambar'])): ?>
                        <img src="<?= htmlspecialchars($berita['gambar']); ?>" alt="<?= htmlspecialchars($berita['judul'] ?? 'Gambar Berita'); ?>" class="detail-banner-img">
                    <?php endif; ?>
                    <?php if (!empty($berita['alamat'])): ?>
                        <div class="detail-block">
                            <h3><i class="fa-solid fa-location-dot"></i> Alamat</h3>
                            <p class="detail-lead"><?= nl2br(htmlspecialchars($berita['alamat'])); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($berita['isi'])): ?>
                        <div class="detail-block">
                            <h3><i class="fa-solid fa-align-left"></i> Berita Selengkapnya</h3>
                            <p><?= nl2br(htmlspecialchars($berita['isi'])); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($berita['penanggung_jawab'])): ?>
                        <div class="detail-block">
                            <h3><i class="fa-solid fa-user-tie"></i> Penanggung Jawab</h3>
                            <ul class="detail-check-list"><?= render_pipe_list($berita['penanggung_jawab']); ?></ul>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($berita['manfaat'])): ?>
                        <div class="detail-block">
                            <h3><i class="fa-solid fa-medal"></i> Manfaat Utama</h3>
                            <ul class="detail-check-list"><?= render_pipe_list($berita['manfaat']); ?></ul>
                        </div>
                    <?php endif; ?>
                </div>
<!-- SIDEBAR -->
            <aside class="detail-sidebar">
                <div class="sidebar-card">
                    <h4><i class="fa-solid fa-circle-info"></i> Informasi Tambahan</h4>
                    <div class="sidebar-info-group">
                        <div class="sidebar-info-item">
                            <div class="sidebar-info-icon"><i class="fa-solid fa-users"></i></div>
                            <div class="sidebar-info-text">
                                <strong>Target Peserta</strong>
                                <span id="detailTarget">Siswa SMK</span>
                            </div>
                        </div>
                        <div class="sidebar-info-item">
                            <div class="sidebar-info-icon"><i class="fa-solid fa-calendar-days"></i></div>
                            <div class="sidebar-info-text">
                                <strong>Frekuensi / Pelaksanaan</strong>
                                <span id="detailFrekuensi">Berkala</span>
                            </div>
                        </div>
                        <div class="sidebar-info-item">
                            <div class="sidebar-info-icon"><i class="fa-solid fa-user-tie"></i></div>
                            <div class="sidebar-info-text">
                                <strong>Penanggung Jawab</strong>
                                <span id="detailPenanggungJawab">Tim Humas SMK</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-card"
                    style="background: linear-gradient(135deg, var(--primary-navy), var(--primary-slate)); color: white;">
                    <h4 style="color: white; border-color: rgba(255,255,255,0.1);"><i class="fa-solid fa-headset"
                            style="color: var(--brand-gold);"></i> Butuh Informasi?</h4>
                    <p style="font-size: 14px; color: #cbd5e1; margin-bottom: 20px; line-height: 1.6;">
                        Ada pertanyaan mengenai program ini? Hubungi tim Humas SMK untuk konsultasi atau kemitraan.
                    </p>
                    <a href="index.php" class="btn" style="width: 100%; font-size: 14px; padding: 12px;">
                        <i class="fa-solid fa-envelope"></i> Hubungi Humas
                    </a>
                </div>
            </aside>
            </div>
        </main>
    <?php else: ?>
        <?php include __DIR__ . '/../components/detail-not-found.php'; ?>
    <?php endif; ?>

    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script src="../js/script.js"></script>
</body>
</html>
