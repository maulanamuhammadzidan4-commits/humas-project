<?php
/**
 * Handler CRUD — Lowongan Kerja
 */
session_start();
require_once '../../backend/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_admin.php");
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'tambah') {
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO lowongan_kerja (id_perusahaan, posisi, deskripsi, kuota, batas_daftar, status_loker)
             VALUES (?,?,?,?,?,?)"
        );
        mysqli_stmt_bind_param($stmt, 'issisi',
            $_POST['id_perusahaan'], $_POST['posisi'], $_POST['deskripsi'],
            $_POST['kuota'], $_POST['batas_daftar'], $_POST['status_loker']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Lowongan kerja berhasil ditambahkan!");
        header("Location: ../lowongan.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        $stmt = mysqli_prepare($koneksi,
            "UPDATE lowongan_kerja SET id_perusahaan=?, posisi=?, deskripsi=?, kuota=?, batas_daftar=?, status_loker=?
             WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, 'ississi',
            $_POST['id_perusahaan'], $_POST['posisi'], $_POST['deskripsi'],
            $_POST['kuota'], $_POST['batas_daftar'], $_POST['status_loker'], $_POST['id']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Lowongan berhasil diperbarui!");
        header("Location: ../lowongan.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM lowongan_kerja WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Lowongan berhasil dihapus.");
        header("Location: ../lowongan.php?msg=$msg&type=success");

    } else {
        header("Location: ../lowongan.php");
    }
} catch (Exception $e) {
    $msg = urlencode("Gagal: " . $e->getMessage());
    header("Location: ../lowongan.php?msg=$msg&type=danger");
}
exit;
