<?php
/**
 * Header top bar admin panel — Humas SMK
 * @param string $page_title  Judul halaman yang ditampilkan
 */
$page_title = $page_title ?? 'Dashboard';
?>
<header class="admin-header">
    <!-- Toggle Sidebar Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Breadcrumb / Title -->
    <div class="header-title">
        <h1><?= htmlspecialchars($page_title) ?></h1>
    </div>

    <!-- Right Side -->
    <div class="header-right">
        <!-- Date -->
        <div class="header-date">
            <i class="fa-regular fa-calendar"></i>
            <span id="headerDate"></span>
        </div>

        <!-- User Dropdown -->
        <div class="header-user" id="userDropdownTrigger">
            <div class="header-avatar">
                <?= strtoupper(substr($_SESSION['nama_lengkap'] ?? 'A', 0, 1)) ?>
            </div>
            <div class="header-user-info">
                <span class="user-name"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '-') ?></span>
                <span class="user-role"><?= htmlspecialchars($_SESSION['jabatan'] ?? 'Admin') ?></span>
            </div>
            <i class="fa-solid fa-chevron-down header-chevron"></i>

            <!-- Dropdown -->
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-info">
                    <div class="dropdown-avatar">
                        <?= strtoupper(substr($_SESSION['nama_lengkap'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <p class="dropdown-name"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '-') ?></p>
                        <p class="dropdown-role"><?= htmlspecialchars($_SESSION['jabatan'] ?? 'Admin') ?></p>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="users.php" class="dropdown-item">
                    <i class="fa-solid fa-user-pen"></i> Edit Profil
                </a>
                <a href="../frontend/index.php" class="dropdown-item" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item dropdown-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </a>
            </div>
        </div>
    </div>
</header>

<script>
// Tanggal dinamis di header
const headerDate = document.getElementById('headerDate');
if (headerDate) {
    const now = new Date();
    headerDate.textContent = now.toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
}

// Toggle dropdown user
const trigger = document.getElementById('userDropdownTrigger');
const dropdown = document.getElementById('userDropdown');
if (trigger && dropdown) {
    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        trigger.classList.toggle('active');
    });
    document.addEventListener('click', () => {
        dropdown.classList.remove('show');
        trigger.classList.remove('active');
    });
}
</script>
