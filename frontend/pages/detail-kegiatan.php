<?php
require_once __DIR__ . '/../config.php'; // Include the config.php file for BASE_URL and ROOT_PATH definitions
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kegiatan Humas | Humas SMK</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- DETAIL HEADER -->
    <header class="detail-header">
        <div class="detail-header-content">
            <a href="<?= BASE_URL ?>frontend/index.php#kegiatan" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Kegiatan
            </a>
            <div class="detail-header-title">
                <div class="detail-header-icon" id="detailIcon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="detail-header-text">
                    <span class="section-tag" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;"
                        id="detailBadge">PROGRAM UNGGULAN</span>
                    <h1 id="detailTitle">Detail Kegiatan Humas</h1>
                    <p id="detailTagline">Informasi lengkap kegiatan dan program kemitraan Humas SMK</p>
                </div>
            </div>
        </div>
    </header>

    <!-- DETAIL MAIN CONTENT -->
    <main class="detail-container">
        <div class="detail-grid">
            <div class="detail-main">
                <img src="" alt="Banner Kegiatan" class="detail-banner-img" id="detailBanner">
                <div class="detail-block">
                    <h3><i class="fa-solid fa-file-lines"></i> Deskripsi Lengkap</h3>
                    <p id="detailDeskripsi">Memuat deskripsi kegiatan...</p>
                </div>
                <div class="detail-block">
                    <h3><i class="fa-solid fa-bullseye"></i> Tujuan Kegiatan</h3>
                    <ul class="detail-check-list" id="detailTujuan"></ul>
                </div>
                <div class="detail-block">
                    <h3><i class="fa-solid fa-medal"></i> Manfaat Utama</h3>
                    <ul class="detail-check-list" id="detailManfaat"></ul>
                </div>
                <div class="detail-block">
                    <h3><i class="fa-solid fa-list-check"></i> Bentuk Kegiatan</h3>
                    <ul class="detail-check-list" id="detailBentuk"></ul>
                </div>
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
                <div class="sidebar-card sidebar-card-dark">
                    <h4 class="sidebar-card-dark-title">
                        <i class="fa-solid fa-headset"></i> Butuh Informasi?
                    </h4>
                    <p class="sidebar-card-dark-text">
                        Ada pertanyaan mengenai program ini? Hubungi tim Humas SMK untuk konsultasi atau kemitraan.
                    </p>
                    <a href="../index.php#kontak" class="btn sidebar-card-dark-button">
                        <i class="fa-solid fa-envelope"></i> Hubungi Humas
                    </a>
                </div>
            </aside>
        </div>
    </main>

    <!-- FOOTER -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- DYNAMIC SCRIPT -->
    <script src="../js/detail-kegiatan.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>