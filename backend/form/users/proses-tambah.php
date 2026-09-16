<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $password_raw = trim($_POST['password'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $jabatan      = trim($_POST['jabatan'] ?? 'Staf Humas');

    if (empty($username) || empty($password_raw) || empty($nama_lengkap)) {
        $_SESSION['flash_error'] = "Username, Password, dan Nama Lengkap wajib diisi!";
        header("Location: ../../../admin/form/users-form.php");
        exit();
    }

    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

    try {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama_lengkap, jabatan) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $username, $password_hashed, $nama_lengkap, $jabatan);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "User baru berhasil ditambahkan!";
        header("Location: ../../../admin/users.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['flash_error'] = "Gagal: Username sudah digunakan.";
        } else {
            $_SESSION['flash_error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
        header("Location: ../../../admin/form/users-form.php");
        exit();
    }
} else {
    header("Location: ../../../admin/users.php");
    exit();
}
