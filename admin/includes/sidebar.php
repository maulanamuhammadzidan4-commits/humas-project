<?php
/**
 * Sidebar navigasi admin panel — Humas SMK
 */
$current = basename($_SERVER['PHP_SELF']);
function nav_active($pages) {
    global $current;
    $pages = is_array($pages) ? $pages : [$pages];
    return in_array($current, $pages) ? 'active' : '';
}
?>
<aside class="admin-sidebar" id="adminSidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fa-solid fa-school-flag"></i>
        </div>
        <div class="sidebar-logo-text">
            <span class="sidebar-brand">Humas SMK</span>
            <span class="sidebar-sub">Admin Panel</span>
        </div>
        <button class="sidebar-close-btn" id="sidebarCloseBtn" onclick="toggleSidebar()">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>

        <a href="dashboard.php" class="nav-item <?= nav_active('dashboard.php') ?>">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-section-label">Data Master</div>

        <a href="perusahaan.php" class="nav-item <?= nav_active('perusahaan.php') ?>">
            <i class="fa-solid fa-building"></i>
            <span>Perusahaan Mitra</span>
        </a>

        <a href="lowongan.php" class="nav-item <?= nav_active('lowongan.php') ?>">
            <i class="fa-solid fa-briefcase"></i>
            <span>Lowongan Kerja</span>
        </a>

        <a href="siswa.php" class="nav-item <?= nav_active('siswa.php') ?>">
            <i class="fa-solid fa-user-graduate"></i>
            <span>Data Siswa</span>
        </a>

        <div class="nav-section-label">Program</div>

        <a href="pkl.php" class="nav-item <?= nav_active('pkl.php') ?>">
            <i class="fa-solid fa-map-location-dot"></i>
            <span>Penempatan PKL</span>
        </a>

        <a href="tracer.php" class="nav-item <?= nav_active('tracer.php') ?>">
            <i class="fa-solid fa-route"></i>
            <span>Tracer Study</span>
        </a>

        <div class="nav-section-label">Sistem</div>

        <a href="users.php" class="nav-item <?= nav_active('users.php') ?>">
            <i class="fa-solid fa-users-gear"></i>
            <span>Manajemen User</span>
        </a>

        <a href="logout.php" class="nav-item nav-logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Keluar</span>
        </a>
    </nav>
</aside>

<!-- Overlay untuk mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
