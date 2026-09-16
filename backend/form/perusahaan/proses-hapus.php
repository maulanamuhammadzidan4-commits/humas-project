<?php
session_start();
require_once "../../connection.php";

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id > 0) {
    try {
        // Cek ketergantungan penempatan_pkl (karena FK restrict)
        $cek_pkl = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM penempatan_pkl WHERE id_perusahaan = $id");
        $data_pkl = mysqli_fetch_assoc($cek_pkl);
        if (($data_pkl['jml'] ?? 0) > 0) {
            $_SESSION['flash_error'] = "Tidak dapat menghapus perusahaan ini karena masih terkait dengan data Penempatan PKL siswa!";
            header("Location: ../../../admin/perusahaan.php");
            exit();
        }

        $stmt = mysqli_prepare($koneksi, "DELETE FROM perusahaan WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data perusahaan berhasil dihapus!";
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash_error'] = "Gagal menghapus perusahaan: " . $e->getMessage();
    }
} else {
    $_SESSION['flash_error'] = "ID Perusahaan tidak valid!";
}

header("Location: ../../../admin/perusahaan.php");
exit();
