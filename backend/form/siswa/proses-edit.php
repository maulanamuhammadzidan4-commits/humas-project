<?php
session_start();
require_once "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = intval($_POST['id'] ?? 0);
    $nisn          = trim($_POST['nisn'] ?? '');
    $nama          = trim($_POST['nama'] ?? '');
    $kelas         = trim($_POST['kelas'] ?? '');
    $jurusan       = trim($_POST['jurusan'] ?? '');
    $status_alumni = isset($_POST['status_alumni']) ? intval($_POST['status_alumni']) : 0;

    if ($id <= 0 || empty($nisn) || empty($nama) || empty($kelas) || empty($jurusan)) {
        $_SESSION['flash_error'] = "Data tidak lengkap atau ID tidak valid!";
        header("Location: ../../../admin/form/siswa-form.php?id=" . $id);
        exit();
    }

    try {
        $stmt = mysqli_prepare($koneksi, "UPDATE siswa SET nisn = ?, nama = ?, kelas = ?, jurusan = ?, status_alumni = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssii", $nisn, $nama, $kelas, $jurusan, $status_alumni, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Data siswa berhasil diperbarui!";
        header("Location: ../../../admin/siswa.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['flash_error'] = "Gagal: NISN sudah digunakan oleh siswa lain.";
        } else {
            $_SESSION['flash_error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
        header("Location: ../../../admin/form/siswa-form.php?id=" . $id);
        exit();
    }
} else {
    header("Location: ../../../admin/siswa.php");
    exit();
}
