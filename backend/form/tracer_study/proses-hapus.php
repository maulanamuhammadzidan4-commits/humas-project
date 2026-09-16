<?php
session_start();
require_once "../../connection.php";

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM tracer_study WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data Tracer Study berhasil dihapus!";
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash_error'] = "Gagal menghapus Tracer Study: " . $e->getMessage();
    }
} else {
    $_SESSION['flash_error'] = "ID Tracer Study tidak valid!";
}

header("Location: ../../../admin/tracer_study.php");
exit();
