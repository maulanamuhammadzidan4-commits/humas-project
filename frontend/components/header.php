<?php
require_once __DIR__ . '/../../config.php';
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
                    Login
                </a>
            </li>
        </ul>

        <button class="mobile-toggle" id="menuToggle" aria-label="Toggle Mobile Navigation">
            <i class="fa-solid fa-bars" id="toggleIcon"></i>
        </button>
    </nav>