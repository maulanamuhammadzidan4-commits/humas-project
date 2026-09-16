<?php
session_start();
require_once "../../connection.php";

$id_user = intval($_GET['id_user'] ?? $_POST['id_user'] ?? 0);

if ($id_user > 0) {
    try {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, "i", $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data user berhasil dihapus!";
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash_error'] = "Gagal menghapus user: " . $e->getMessage();
    }
} else {
    $_SESSION['flash_error'] = "ID User tidak valid!";
}

header("Location: ../../../admin/users.php");
exit();
