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

    <div class="sidebar-card" style="background: linear-gradient(135deg, var(--primary-navy), var(--primary-slate)); color: white;">
        <h4 style="color: white; border-color: rgba(255,255,255,0.1);"><i class="fa-solid fa-share-nodes" style="color: var(--brand-gold);"></i> Bagikan Informasi</h4>
        <p style="font-size: 14px; color: #cbd5e1; margin-bottom: 20px; line-height: 1.6;">
            Bagikan berita resmi ini kepada rekan, siswa, dan wali murid.
        </p>
        <a href="../index.php#berita" class="btn" style="width: 100%; font-size: 14px; padding: 12px; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3);">
            <i class="fa-solid fa-newspaper"></i> Lihat Berita Lainnya
        </a>
    </div>
</aside>