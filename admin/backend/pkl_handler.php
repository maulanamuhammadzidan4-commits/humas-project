<?php
/**
 * Handler CRUD — Penempatan PKL
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
            "INSERT INTO penempatan_pkl (id_siswa, id_perusahaan, pembimbing, tanggal_mulai, tanggal_selesai, status_penempatan)
             VALUES (?,?,?,?,?,?)"
        );
        mysqli_stmt_bind_param($stmt, 'iissss',
            $_POST['id_siswa'], $_POST['id_perusahaan'], $_POST['pembimbing'],
            $_POST['tanggal_mulai'], $_POST['tanggal_selesai'], $_POST['status_penempatan']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data PKL berhasil ditambahkan!");
        header("Location: ../pkl.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        $stmt = mysqli_prepare($koneksi,
            "UPDATE penempatan_pkl SET id_siswa=?, id_perusahaan=?, pembimbing=?, tanggal_mulai=?, tanggal_selesai=?, status_penempatan=?
             WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, 'iissssi',
            $_POST['id_siswa'], $_POST['id_perusahaan'], $_POST['pembimbing'],
            $_POST['tanggal_mulai'], $_POST['tanggal_selesai'], $_POST['status_penempatan'], $_POST['id']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data PKL berhasil diperbarui!");
        header("Location: ../pkl.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM penempatan_pkl WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data PKL berhasil dihapus.");
        header("Location: ../pkl.php?msg=$msg&type=success");

    } else {
        header("Location: ../pkl.php");
    }
} catch (Exception $e) {
    $msg = urlencode("Gagal: " . $e->getMessage());
    header("Location: ../pkl.php?msg=$msg&type=danger");
}
exit;
