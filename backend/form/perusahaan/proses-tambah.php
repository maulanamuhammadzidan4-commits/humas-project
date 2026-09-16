<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama             = trim($_POST['nama'] ?? '');
    $sektor_bidang    = trim($_POST['sektor_bidang'] ?? '');
    $alamat           = trim($_POST['alamat'] ?? '');
    $penanggung_jawab = trim($_POST['penanggung_jawab'] ?? '');
    $no_telepon       = trim($_POST['no_telepon'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $status_mou       = $_POST['status_mou'] ?? 'Proses';

    if (empty($nama) || empty($email)) {
        $_SESSION['flash_error'] = "Nama dan Email perusahaan wajib diisi!";
        header("Location: ../../../admin/form/perusahaan-form.php");
        exit();
    }

    try {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO perusahaan (nama, sektor_bidang, alamat, penanggung_jawab, no_telepon, email, status_mou) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssssss", $nama, $sektor_bidang, $alamat, $penanggung_jawab, $no_telepon, $email, $status_mou);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data perusahaan berhasil ditambahkan!";
        header("Location: ../../../admin/perusahaan.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['flash_error'] = "Gagal: Email sudah digunakan oleh perusahaan lain.";
        } else {
            $_SESSION['flash_error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
        header("Location: ../../../admin/form/perusahaan-form.php");
        exit();
    }
} else {
    header("Location: ../../../admin/perusahaan.php");
    exit();
}
