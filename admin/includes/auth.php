<?php
/**
 * Auth Guard — wajib di-include di setiap halaman admin.
 * Redirect ke login jika session tidak aktif.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: " . str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count(dirname($_SERVER['PHP_SELF']), '/')) . "admin/login_admin.php");
    // Fallback sederhana
    header("Location: ../login_admin.php");
    exit;
}

// Hapus flash login success supaya tidak loop
if (isset($_SESSION['login_success_flash'])) {
    unset($_SESSION['login_success_flash']);
}
