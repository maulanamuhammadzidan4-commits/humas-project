<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_perusahaan = intval($_POST['id_perusahaan'] ?? 0);
    $posisi        = trim($_POST['posisi'] ?? '');
    $deskripsi     = trim($_POST['deskripsi'] ?? '');
    $kuota         = intval($_POST['kuota'] ?? 1);
    $batas_daftar  = $_POST['batas_daftar'] ?? '';
    $status_loker  = $_POST['status_loker'] ?? 'Buka';

    if ($id_perusahaan <= 0 || empty($posisi) || empty($batas_daftar)) {
        $_SESSION['flash_error'] = "Perusahaan, posisi, dan batas pendaftaran wajib diisi!";
        header("Location: ../../../admin/form/lowongan-kerja-form.php");
        exit();
    }

    try {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO lowongan_kerja (id_perusahaan, posisi, deskripsi, kuota, batas_daftar, status_loker) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ississ", $id_perusahaan, $posisi, $deskripsi, $kuota, $batas_daftar, $status_loker);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Lowongan kerja baru berhasil ditambahkan!";
        header("Location: ../../../admin/lowongan_kerja.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash_error'] = "Gagal menambahkan lowongan kerja: " . $e->getMessage();
        header("Location: ../../../admin/form/lowongan-kerja-form.php");
        exit();
    }
} else {
    header("Location: ../../../admin/lowongan_kerja.php");
    exit();
}
