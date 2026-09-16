<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa           = intval($_POST['id_siswa'] ?? 0);
    $tahun_lulus        = intval($_POST['tahun_lulus'] ?? date('Y'));
    $status_alumni      = $_POST['status_alumni'] ?? 'Mencari Kerja';
    $nama_instansi      = trim($_POST['nama_instansi'] ?? '');
    $pendapatan_bulanan = !empty($_POST['pendapatan_bulanan']) ? intval($_POST['pendapatan_bulanan']) : null;

    if ($id_siswa <= 0 || $tahun_lulus < 1990) {
        $_SESSION['flash_error'] = "Siswa dan Tahun Lulus wajib diisi dengan benar!";
        header("Location: ../../../admin/form/tracer-study-form.php");
        exit();
    }

    try {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO tracer_study (id_siswa, tahun_lulus, status_alumni, nama_instansi, pendapatan_bulanan) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iissi", $id_siswa, $tahun_lulus, $status_alumni, $nama_instansi, $pendapatan_bulanan);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Otomatis update status_alumni siswa jadi 1 (Alumni)
        mysqli_query($koneksi, "UPDATE siswa SET status_alumni = 1 WHERE id = $id_siswa");

        $_SESSION['flash_success'] = "Data Tracer Study alumni berhasil ditambahkan!";
        header("Location: ../../../admin/tracer_study.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['flash_error'] = "Siswa tersebut sudah memiliki data Tracer Study. Gunakan menu Edit untuk mengubahnya.";
        } else {
            $_SESSION['flash_error'] = "Gagal menambahkan Tracer Study: " . $e->getMessage();
        }
        header("Location: ../../../admin/form/tracer-study-form.php");
        exit();
    }
} else {
    header("Location: ../../../admin/tracer_study.php");
    exit();
}
