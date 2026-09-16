<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user      = intval($_POST['id_user'] ?? 0);
    $username     = trim($_POST['username'] ?? '');
    $password_raw = trim($_POST['password'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $jabatan      = trim($_POST['jabatan'] ?? 'Staf Humas');

    if ($id_user <= 0 || empty($username) || empty($nama_lengkap)) {
        $_SESSION['flash_error'] = "Data tidak lengkap atau ID tidak valid!";
        header("Location: ../../../admin/form/users-form.php?id=" . $id_user);
        exit();
    }

    try {
        if (!empty($password_raw)) {
            $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($koneksi, "UPDATE users SET username = ?, password = ?, nama_lengkap = ?, jabatan = ? WHERE id_user = ?");
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $password_hashed, $nama_lengkap, $jabatan, $id_user);
        } else {
            $stmt = mysqli_prepare($koneksi, "UPDATE users SET username = ?, nama_lengkap = ?, jabatan = ? WHERE id_user = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $username, $nama_lengkap, $jabatan, $id_user);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data user berhasil diperbarui!";
        header("Location: ../../../admin/users.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['flash_error'] = "Gagal: Username sudah digunakan oleh user lain.";
        } else {
            $_SESSION['flash_error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
        header("Location: ../../../admin/form/users-form.php?id=" . $id_user);
        exit();
    }
} else {
    header("Location: ../../../admin/users.php");
    exit();
}
