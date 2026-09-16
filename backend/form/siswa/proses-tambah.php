<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn          = trim($_POST['nisn'] ?? '');
    $nama          = trim($_POST['nama'] ?? '');
    $kelas         = trim($_POST['kelas'] ?? '');
    $jurusan       = trim($_POST['jurusan'] ?? '');
    $status_alumni = isset($_POST['status_alumni']) ? intval($_POST['status_alumni']) : 0;

    if (empty($nisn) || empty($nama) || empty($kelas) || empty($jurusan)) {
        $_SESSION['flash_error'] = "NISN, Nama, Kelas, dan Jurusan wajib diisi!";
        header("Location: ../../../admin/form/siswa-form.php");
        exit();
    }

    try {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO siswa (nisn, nama, kelas, jurusan, status_alumni) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssi", $nisn, $nama, $kelas, $jurusan, $status_alumni);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data siswa berhasil ditambahkan!";
        header("Location: ../../../admin/siswa.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['flash_error'] = "Gagal: NISN sudah terdaftar pada siswa lain.";
        } else {
            $_SESSION['flash_error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
        header("Location: ../../../admin/form/siswa-form.php");
        exit();
    }
} else {
    header("Location: ../../../admin/siswa.php");
    exit();
}
