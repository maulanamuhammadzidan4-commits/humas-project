<?php require_once __DIR__ . '/../../backend/helpers.php'; ?>
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