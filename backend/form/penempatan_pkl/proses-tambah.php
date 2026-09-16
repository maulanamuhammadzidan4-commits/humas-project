<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa          = intval($_POST['id_siswa'] ?? 0);
    $id_perusahaan     = intval($_POST['id_perusahaan'] ?? 0);
    $pembimbing        = trim($_POST['pembimbing'] ?? '');
    $tanggal_mulai     = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai   = $_POST['tanggal_selesai'] ?? '';
    $status_penempatan = $_POST['status_penempatan'] ?? 'Draft';

    if ($id_siswa <= 0 || $id_perusahaan <= 0 || empty($pembimbing) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
        $_SESSION['flash_error'] = "Siswa, Perusahaan, Pembimbing, dan Periode tanggal wajib diisi!";
        header("Location: ../../../admin/form/penempatan-pkl-form.php");
        exit();
    }

    try {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO penempatan_pkl (id_siswa, id_perusahaan, pembimbing, tanggal_mulai, tanggal_selesai, status_penempatan) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iissss", $id_siswa, $id_perusahaan, $pembimbing, $tanggal_mulai, $tanggal_selesai, $status_penempatan);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data penempatan PKL berhasil ditambahkan!";
        header("Location: ../../../admin/penempatan_pkl.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash_error'] = "Gagal menambahkan penempatan PKL: " . $e->getMessage();
        header("Location: ../../../admin/form/penempatan-pkl-form.php");
        exit();
    }
} else {
    header("Location: ../../../admin/penempatan_pkl.php");
    exit();
}
