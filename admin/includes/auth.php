<?php
/**
 * Auth Guard — wajib di-include di setiap halaman admin.
 * Redirect ke login jika session tidak aktif.
 * Letakkan include ini setelah session_start() di tiap halaman admin.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Semua halaman admin ada di folder admin/, login_admin.php juga di sana
    header("Location: login_admin.php");
    exit;
}

// Hapus flash login success supaya tidak loop
if (isset($_SESSION['login_success_flash'])) {
    unset($_SESSION['login_success_flash']);
}
