<?php
session_start();
require_once "../../connection.php";

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM lowongan_kerja WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Lowongan kerja berhasil dihapus!";
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash_error'] = "Gagal menghapus lowongan kerja: " . $e->getMessage();
    }
} else {
    $_SESSION['flash_error'] = "ID Lowongan tidak valid!";
}

header("Location: ../../../admin/lowongan_kerja.php");
exit();
