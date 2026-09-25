<?php
include __DIR__ . '/../../config.php'; // Include the config.php file for BASE_URL and ROOT_PATH definitions
?>

    <nav>
        <div class="logo">
            <i class="fa-solid fa-graduation-cap"></i>
            HUMAS <span>SMK</span>
        </div>

        <ul class="menu" id="navMenu">
            <li><a href="<?= BASE_URL ?>frontend/index.php#beranda">Beranda</a></li>
            <li><a href="<?= BASE_URL ?>frontend/index.php#tentang">Tentang</a></li>
            <li><a href="<?= BASE_URL ?>frontend/index.php#kegiatan">Kegiatan</a></li>
            <li><a href="<?= BASE_URL ?>frontend/index.php#berita">Berita</a></li>
            <li><a href="<?= BASE_URL ?>frontend/index.php#staff">Staff</a></li>
            <li><a href="<?= BASE_URL ?>frontend/index.php#kontak">Kontak</a></li>
            <li>
                <a href="<?= BASE_URL ?>admin/login_admin.php" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i> Login Admin
                </a>
            </li>
        </ul>

        <button class="mobile-toggle" id="menuToggle" aria-label="Toggle Mobile Navigation">
            <i class="fa-solid fa-bars" id="toggleIcon"></i>
        </button>
    </nav>