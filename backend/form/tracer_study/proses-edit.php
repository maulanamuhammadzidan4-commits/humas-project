<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id                 = intval($_POST['id'] ?? 0);
    $id_siswa           = intval($_POST['id_siswa'] ?? 0);
    $tahun_lulus        = intval($_POST['tahun_lulus'] ?? date('Y'));
    $status_alumni      = $_POST['status_alumni'] ?? 'Mencari Kerja';
    $nama_instansi      = trim($_POST['nama_instansi'] ?? '');
    $pendapatan_bulanan = !empty($_POST['pendapatan_bulanan']) ? intval($_POST['pendapatan_bulanan']) : null;

    if ($id <= 0 || $id_siswa <= 0 || $tahun_lulus < 1990) {
        $_SESSION['flash_error'] = "Data tidak lengkap atau ID tidak valid!";
        header("Location: ../../../admin/form/tracer-study-form.php?id=" . $id);
        exit();
    }

    try {
        $stmt = mysqli_prepare($koneksi, "UPDATE tracer_study SET id_siswa = ?, tahun_lulus = ?, status_alumni = ?, nama_instansi = ?, pendapatan_bulanan = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "iissii", $id_siswa, $tahun_lulus, $status_alumni, $nama_instansi, $pendapatan_bulanan, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data Tracer Study berhasil diperbarui!";
        header("Location: ../../../admin/tracer_study.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['flash_error'] = "Siswa tersebut sudah memiliki data Tracer Study lain.";
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui Tracer Study: " . $e->getMessage();
        }
        header("Location: ../../../admin/form/tracer-study-form.php?id=" . $id);
        exit();
    }
} else {
    header("Location: ../../../admin/tracer_study.php");
    exit();
}
