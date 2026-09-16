<?php
session_start();
require_once "../../connection.php";

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM penempatan_pkl WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data penempatan PKL berhasil dihapus!";
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash_error'] = "Gagal menghapus penempatan PKL: " . $e->getMessage();
    }
} else {
    $_SESSION['flash_error'] = "ID Penempatan PKL tidak valid!";
}

header("Location: ../../../admin/penempatan_pkl.php");
exit();
