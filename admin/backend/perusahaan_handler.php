<?php
/**
 * Handler CRUD — Perusahaan Mitra
 * POST params: action (tambah|edit|hapus), lalu field tabel
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
            "INSERT INTO perusahaan (nama, sektor_bidang, alamat, penanggung_jawab, no_telepon, email, status_mou)
             VALUES (?,?,?,?,?,?,?)"
        );
        mysqli_stmt_bind_param($stmt, 'sssssss',
            $_POST['nama'], $_POST['sektor_bidang'], $_POST['alamat'],
            $_POST['penanggung_jawab'], $_POST['no_telepon'], $_POST['email'], $_POST['status_mou']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Perusahaan berhasil ditambahkan!");
        header("Location: ../perusahaan.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        $stmt = mysqli_prepare($koneksi,
            "UPDATE perusahaan SET nama=?, sektor_bidang=?, alamat=?, penanggung_jawab=?, no_telepon=?, email=?, status_mou=?
             WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, 'sssssssi',
            $_POST['nama'], $_POST['sektor_bidang'], $_POST['alamat'],
            $_POST['penanggung_jawab'], $_POST['no_telepon'], $_POST['email'],
            $_POST['status_mou'], $_POST['id']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data perusahaan berhasil diperbarui!");
        header("Location: ../perusahaan.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM perusahaan WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Perusahaan berhasil dihapus.");
        header("Location: ../perusahaan.php?msg=$msg&type=success");

    } else {
        header("Location: ../perusahaan.php");
    }
} catch (Exception $e) {
    $msg = urlencode("Gagal: " . $e->getMessage());
    header("Location: ../perusahaan.php?msg=$msg&type=danger");
}
exit;
