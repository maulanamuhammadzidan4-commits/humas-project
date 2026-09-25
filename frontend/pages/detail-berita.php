<?php
session_start();
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
$isAdmin = isset($_SESSION['user_id']);
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$berita = getBeritaById($koneksi, $id);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $berita ? htmlspecialchars($berita['judul']) : 'Berita Tidak Ditemukan'; ?> | Humas SMK</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="../assets/css/style_pages.css">
</head>

<body>

    <!-- NAVBAR -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <?php if (isset($berita) && $berita): ?>
        <!-- DETAIL HEADER -->
        <header class="detail-header">
            <div class="detail-header-content">
                <a href="../index.php#berita" class="back-btn">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Berita
                </a>
                <div class="detail-header-title">
                    <div class="detail-header-icon">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                    <div class="detail-header-text">
                            <span class="section-tag detail-category">
                            <?= htmlspecialchars($berita['kategori'] ?? 'BERITA'); ?>
                        </span>
                        <h1><?= htmlspecialchars($berita['judul']); ?></h1>
                        <p><i class="fa-regular fa-calendar-check"></i> <?= formatTanggalIndo($berita['tanggal']); ?> &bull; DIPUBLIKASIKAN OLEH HUMAS SMK</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- DETAIL MAIN CONTENT -->
        <main class="detail-container">
            <div class="detail-grid">
                <div class="detail-main">
                    <img src="<?= htmlspecialchars($berita['gambar']); ?>" alt="<?= htmlspecialchars($berita['judul']); ?>" class="detail-banner-img">

                    <div class="detail-block">
                        <h3><i class="fa-solid fa-file-lines"></i> Ringkasan Berita</h3>
                        <p style="font-size: 17px; font-weight: 600; color: var(--brand-blue); line-height: 1.7;">
                            <?= htmlspecialchars($berita['deskripsi']); ?>
                        </p>
                    </div>

                    <div class="detail-block">
                        <h3><i class="fa-solid fa-align-left"></i> Berita Selengkapnya</h3>
                        <p><?= nl2br(htmlspecialchars($berita['isi'])); ?></p>
                    </div>

                    <?php if (!empty($berita['tujuan'])): ?>
                        <div class="detail-block">
                            <h3><i class="fa-solid fa-bullseye"></i> Tujuan Kegiatan</h3>
                            <ul class="detail-check-list">
                                <?php 
                                $tujuanItems = explode('|', $berita['tujuan']);
                                foreach ($tujuanItems as $item): 
                                    if (trim($item) === '') continue;
                                ?>
                                    <li><i class="fa-solid fa-check"></i> <span><?= htmlspecialchars(trim($item)); ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($berita['manfaat'])): ?>
                        <div class="detail-block">
                            <h3><i class="fa-solid fa-medal"></i> Manfaat Utama</h3>
                            <ul class="detail-check-list">
                                <?php 
                                $manfaatItems = explode('|', $berita['manfaat']);
                                foreach ($manfaatItems as $item): 
                                    if (trim($item) === '') continue;
                                ?>
                                    <li><i class="fa-solid fa-check"></i> <span><?= htmlspecialchars(trim($item)); ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- SIDEBAR -->
                <aside class="detail-sidebar">
                    <div class="sidebar-card">
                        <h4><i class="fa-solid fa-circle-info"></i> Informasi Publikasi</h4>
                        <div class="sidebar-info-group">
                            <div class="sidebar-info-item">
                                <div class="sidebar-info-icon"><i class="fa-solid fa-tag"></i></div>
                                <div class="sidebar-info-text">
                                    <strong>Kategori</strong>
                                    <span><?= htmlspecialchars($berita['kategori']); ?></span>
                                </div>
                            </div>

                            <div class="sidebar-info-item">
                                <div class="sidebar-info-icon"><i class="fa-solid fa-calendar-days"></i></div>
                                <div class="sidebar-info-text">
                                    <strong>Tanggal Terbit</strong>
                                    <span><?= formatTanggalIndo($berita['tanggal']); ?></span>
                                </div>
                            </div>

                            <div class="sidebar-info-item">
                                <div class="sidebar-info-icon"><i class="fa-solid fa-user-shield"></i></div>
                                <div class="sidebar-info-text">
                                    <strong>Penulis / Penerbit</strong>
                                    <span>Tim Redaksi Humas SMK</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card sidebar-card-dark">
                        <h4 class="sidebar-card-dark-title"><i class="fa-solid fa-share-nodes"></i> Bagikan Informasi</h4>
                        <p class="sidebar-card-dark-text">
                            Bagikan berita resmi ini kepada rekan, siswa, dan wali murid.
                        </p>
                        <a href="../index.php#berita" class="btn sidebar-card-dark-button">
                            <i class="fa-solid fa-newspaper"></i> Lihat Berita Lainnya
                        </a>
                    </div>
                </aside>
            </div>
        </main>
    <?php else: ?>
        <?php include __DIR__ . '/../components/detail-not-found.php'; ?>
    <?php endif; ?>

    <!-- FOOTER -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="../js/script.js"></script>
</body>
</html>