
<?php
session_start();
require_once '../../backend/connection.php';
$isAdmin = isset($_SESSION['user_id']);
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$berita = null;
function formatTanggalIndo($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }
    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    $timestamp = strtotime($tanggal);
    if (!$timestamp) {
        return htmlspecialchars($tanggal);
    }
    $hari = date('d', $timestamp);
    $bulanNama = $bulan[(int) date('m', $timestamp)];
    $tahun = date('Y', $timestamp);
    return $hari . ' ' . $bulanNama . ' ' . $tahun;
}

if ($id > 0) {

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT * FROM berita WHERE id_berita = ? LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $berita = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>
        <?= $berita
            ? htmlspecialchars($berita['judul'] ?? 'Detail Berita')
            : 'Berita Tidak Ditemukan';
        ?>
        | Humas SMK
    </title>

    <!-- Google Fonts -->
    <link rel="preconnect"
          href="https://fonts.googleapis.com">
    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet"
          href="../assets/css/style.css">
</head>

<body>
<nav>
    <div class="logo">
        <i class="fa-solid fa-graduation-cap"></i>
        HUMAS <span>SMK</span>
    </div>
    <ul class="menu" id="navMenu">
        <li>
            <a href="index.html#beranda">
                Beranda
            </a>
        </li>

        <li>
            <a href="index.html#tentang">
                Tentang
            </a>
        </li>

        <li>
            <a href="index.html#kegiatan">
                Kegiatan
            </a>
        </li>

        <li>
            <a href="index.html#berita">
                Berita
            </a>
        </li>

        <li>
            <a href="index.html#staff">
                Staff
            </a>
        </li>

        <li>
            <a href="index.html#kontak">
                Kontak
            </a>
        </li>

        <li>

            <a href="login.php" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i>
                Login Admin
            </a>
        </li>
    </ul>
    <button
        class="mobile-toggle"
        id="menuToggle"
        aria-label="Toggle Mobile Navigation">

        <i class="fa-solid fa-bars"
           id="toggleIcon"></i>
    </button>
</nav>

<?php if ($berita): ?>

<!-- =========================================================
     DETAIL HEADER
========================================================= -->

<header class="detail-header">

    <div class="detail-header-content">

        <a href="index.html#berita"
           class="back-btn">

            <i class="fa-solid fa-arrow-left"></i>

            Kembali ke Berita

        </a>


        <div class="detail-header-title">

            <div class="detail-header-icon">

                <i class="fa-solid fa-newspaper"></i>

            </div>


            <div class="detail-header-text">

                <span
                    class="section-tag"
                    style="
                        background: rgba(245, 158, 11, 0.2);
                        color: #fbbf24;
                    ">

                    <?= htmlspecialchars(
                        $berita['kategori'] ?? 'BERITA'
                    ); ?>

                </span>


                <h1>

                    <?= htmlspecialchars(
                        $berita['judul'] ?? 'Berita'
                    ); ?>

                </h1>


                <p>

                    <i class="fa-regular fa-calendar-check"></i>

                    <?= formatTanggalIndo(
                        $berita['tanggal'] ?? ''
                    ); ?>

                    &bull;

                    DIPUBLIKASIKAN OLEH HUMAS SMK

                </p>

            </div>

        </div>

    </div>

</header>


<!-- =========================================================
     DETAIL MAIN CONTENT
========================================================= -->

<main class="detail-container">

    <div class="detail-grid">


        <!-- =================================================
             KONTEN UTAMA
        ================================================== -->

        <div class="detail-main">


            <?php if (!empty($berita['gambar'])): ?>

                <img
                    src="<?= htmlspecialchars($berita['gambar']); ?>"
                    alt="<?= htmlspecialchars(
                        $berita['judul'] ?? 'Gambar Berita'
                    ); ?>"
                    class="detail-banner-img">

            <?php endif; ?>


            <!-- ALAMAT -->

            <?php if (!empty($berita['alamat'])): ?>

                <div class="detail-block">

                    <h3>

                        <i class="fa-solid fa-location-dot"></i>

                        Alamat

                    </h3>


                    <p
                        style="
                            font-size: 17px;
                            font-weight: 600;
                            color: var(--brand-blue);
                            line-height: 1.7;
                        ">

                        <?= nl2br(
                            htmlspecialchars(
                                $berita['alamat']
                            )
                        ); ?>

                    </p>

                </div>

            <?php endif; ?>


            <!-- ISI BERITA -->

            <?php if (!empty($berita['isi'])): ?>

                <div class="detail-block">

                    <h3>

                        <i class="fa-solid fa-align-left"></i>

                        Berita Selengkapnya

                    </h3>


                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                $berita['isi']
                            )
                        ); ?>

                    </p>

                </div>

            <?php endif; ?>


            <!-- PENANGGUNG JAWAB -->

            <?php if (!empty($berita['penanggung_jawab'])): ?>

                <div class="detail-block">

                    <h3>

                        <i class="fa-solid fa-user-tie"></i>

                        Penanggung Jawab

                    </h3>


                    <ul class="detail-check-list">

                        <?php

                        $penanggungItems =
                            explode(
                                '|',
                                $berita['penanggung_jawab']
                            );

                        foreach (
                            $penanggungItems
                            as $item
                        ):

                            if (
                                trim($item) === ''
                            ) {
                                continue;
                            }

                        ?>

                            <li>

                                <i class="fa-solid fa-check"></i>

                                <span>

                                    <?= htmlspecialchars(
                                        trim($item)
                                    ); ?>

                                </span>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <!-- MANFAAT -->

            <?php if (!empty($berita['manfaat'])): ?>

                <div class="detail-block">

                    <h3>

                        <i class="fa-solid fa-medal"></i>

                        Manfaat Utama

                    </h3>


                    <ul class="detail-check-list">

                        <?php

                        $manfaatItems =
                            explode(
                                '|',
                                $berita['manfaat']
                            );

                        foreach (
                            $manfaatItems
                            as $item
                        ):

                            if (
                                trim($item) === ''
                            ) {
                                continue;
                            }

                        ?>

                            <li>

                                <i class="fa-solid fa-check"></i>

                                <span>

                                    <?= htmlspecialchars(
                                        trim($item)
                                    ); ?>

                                </span>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


        </div>


        <!-- =================================================
             SIDEBAR
        ================================================== -->

        <aside class="detail-sidebar">


            <!-- INFORMASI PUBLIKASI -->

            <div class="sidebar-card">

                <h4>

                    <i class="fa-solid fa-circle-info"></i>

                    Informasi Publikasi

                </h4>


                <div class="sidebar-info-group">


                    <!-- KATEGORI -->

                    <div class="sidebar-info-item">

                        <div class="sidebar-info-icon">

                            <i class="fa-solid fa-tag"></i>

                        </div>


                        <div class="sidebar-info-text">

                            <strong>
                                Kategori
                            </strong>

                            <span>

                                <?= htmlspecialchars(
                                    $berita['kategori']
                                    ?? '-'
                                ); ?>

                            </span>

                        </div>

                    </div>


                    <!-- TANGGAL -->

                    <div class="sidebar-info-item">

                        <div class="sidebar-info-icon">

                            <i class="fa-solid fa-calendar-days"></i>

                        </div>


                        <div class="sidebar-info-text">

                            <strong>
                                Tanggal Terbit
                            </strong>

                            <span>

                                <?= formatTanggalIndo(
                                    $berita['tanggal']
                                    ?? ''
                                ); ?>

                            </span>

                        </div>

                    </div>


                    <!-- PENULIS -->

                    <div class="sidebar-info-item">

                        <div class="sidebar-info-icon">

                            <i class="fa-solid fa-user-shield"></i>

                        </div>


                        <div class="sidebar-info-text">

                            <strong>
                                Penulis / Penerbit
                            </strong>

                            <span>
                                Tim Redaksi Humas SMK
                            </span>

                        </div>

                    </div>


                </div>

            </div>


            <!-- BAGIKAN -->

            <div
                class="sidebar-card"
                style="
                    background:
                    linear-gradient(
                        135deg,
                        var(--primary-navy),
                        var(--primary-slate)
                    );
                    color: white;
                ">


                <h4
                    style="
                        color: white;
                        border-color:
                        rgba(255,255,255,0.1);
                    ">

                    <i
                        class="fa-solid fa-share-nodes"
                        style="
                            color: var(--brand-gold);
                        ">
                    </i>

                    Bagikan Informasi

                </h4>


                <p
                    style="
                        font-size: 14px;
                        color: #cbd5e1;
                        margin-bottom: 20px;
                        line-height: 1.6;
                    ">

                    Bagikan berita resmi ini
                    kepada rekan, siswa,
                    dan wali murid.

                </p>


                <a
                    href="data_dudi.php"
                    class="btn"
                    style="
                        width: 100%;
                        font-size: 14px;
                        padding: 12px;
                        background:
                        rgba(255,255,255,0.15);
                        color: white;
                        border:
                        1px solid
                        rgba(255,255,255,0.3);
                    ">

                    <i class="fa-solid fa-newspaper"></i>
                    Lihat Berita Lainnya
                </a>
            </div>
        </aside>
    </div>
</main>
<?php else: ?>
<main
    class="detail-container"
    style="
        margin-top: 60px;
        margin-bottom: 120px;
    ">


    <div
        class="detail-main"
        style="
            text-align: center;
            padding: 80px 30px;
        ">


        <div
            style="
                width: 80px;
                height: 80px;
                background: #fee2e2;
                color: #dc2626;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 36px;
                margin: 0 auto 20px;
            ">

            <i class="fa-solid fa-newspaper"></i>

        </div>


        <h2
            style="
                font-size: 28px;
                font-weight: 800;
                color: var(--text-heading);
                margin-bottom: 12px;
            ">

            Berita Tidak Ditemukan

        </h2>


        <p
            style="
                color: var(--text-muted);
                font-size: 16px;
                max-width: 500px;
                margin: 0 auto 30px;
            ">

            Maaf, berita yang Anda cari
            tidak ditemukan atau telah
            dihapus dari sistem kami.

        </p>


        <a
            href="index.html#berita"
            class="btn">

            <i class="fa-solid fa-arrow-left"></i>

            Kembali ke Halaman Utama

        </a>


    </div>

</main>


<?php endif; ?>
<footer>
    <div class="footer-container">
    <div class="footer-col">
            <div
                class="logo"
                style="
                    color: white;
                    margin-bottom: 16px;
                ">
                <i class="fa-solid fa-graduation-cap"></i>
                HUMAS
                <span
                    style="
                        color: var(--brand-gold);
                    ">SMK</span>
            </div>
            <p>
                Portal Resmi Hubungan Masyarakat SMK.
                Berkomitmen membangun sinergi antara
                dunia pendidikan kejuruan dan dunia
                kerja secara berkelanjutan.
            </p>
            <div class="footer-socials">
                <a href="#"
                   aria-label="Facebook">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="#"
                   aria-label="Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="#"
                   aria-label="YouTube">
                    <i class="fa-brands fa-youtube"></i>
                </a>
                <a href="#"
                   aria-label="LinkedIn">
                    <i class="fa-brands fa-linkedin-in"></i>
                </a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Navigasi Cepat</h4>
            <ul class="footer-links">
                <li>
                    <a href="index.html#beranda">
                        <i class="fa-solid fa-chevron-right"></i>
                        Beranda
                    </a>
                </li>

                <li>
                    <a href="index.html#tentang">
                        <i class="fa-solid fa-chevron-right"></i>
                        Tentang Humas
                    </a>
                </li>

                <li>
                    <a href="index.html#kegiatan">
                        <i class="fa-solid fa-chevron-right"></i>
                        Kegiatan
                    </a>
                </li>

                <li>
                    <a href="index.html#berita">
                        <i class="fa-solid fa-chevron-right"></i>
                        Berita Terbaru
                    </a>
                </li>

            </ul>

        </div>


        <div class="footer-col">

            <h4>Program Utama</h4>

            <ul class="footer-links">

                <li>
                    <a href="detail-kegiatan.html?id=kunjungan-industri">
                        <i class="fa-solid fa-chevron-right"></i>
                        Kunjungan Industri
                    </a>
                </li>

                <li>
                    <a href="detail-kegiatan.html?id=kerja-sama-industri">
                        <i class="fa-solid fa-chevron-right"></i>
                        Kemitraan Perusahaan
                    </a>
                </li>

                <li>
                    <a href="detail-kegiatan.html?id=pkl">
                        <i class="fa-solid fa-chevron-right"></i>
                        Program Magang PKL
                    </a>
                </li>

                <li>
                    <a href="login.php">
                        <i class="fa-solid fa-chevron-right"></i>
                        Portal Admin
                    </a>
                </li>

            </ul>

        </div>


        <div class="footer-col">

            <h4>Hubungi Kami</h4>

            <p>

                <i
                    class="fa-solid fa-location-dot"
                    style="
                        color: var(--brand-gold);
                    ">
                </i>

                Jl. Contoh No. 123,
                Jawa Barat

            </p>


            <p>

                <i
                    class="fa-solid fa-phone"
                    style="
                        color: var(--brand-gold);
                    ">
                </i>

                (021) 555-0123

            </p>
            <p><i class="fa-solid fa-envelope" style=" color: var(--brand-gold);"></i>
                humas@smk.sch.id
            </p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>
            &copy; 2026 Humas SMK.
            All Rights Reserved.
        </p>
        <p>
            Website Hubungan Masyarakat &
            Kemitraan Industri Sekolah
        </p>
    </div>
</footer>
<script>

const menuToggle =
    document.getElementById('menuToggle');

const navMenu =
    document.getElementById('navMenu');

const toggleIcon =
    document.getElementById('toggleIcon');


if (menuToggle) {

    menuToggle.addEventListener(
        'click',
        () => {

            navMenu.classList.toggle('active');


            if (
                navMenu.classList.contains('active')
            ) {

                toggleIcon.classList.remove(
                    'fa-bars'
                );

                toggleIcon.classList.add(
                    'fa-xmark'
                );

            } else {

                toggleIcon.classList.remove(
                    'fa-xmark'
                );

                toggleIcon.classList.add(
                    'fa-bars'
                );

            }

        }
    );
}
</script>
</body>
</html>
