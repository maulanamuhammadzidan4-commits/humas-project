<?php
/**
 * Handler CRUD — Siswa
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
        $status_alumni = isset($_POST['status_alumni']) ? 1 : 0;
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO siswa (nisn, nama, kelas, jurusan, status_alumni) VALUES (?,?,?,?,?)"
        );
        mysqli_stmt_bind_param($stmt, 'ssssi',
            $_POST['nisn'], $_POST['nama'], $_POST['kelas'], $_POST['jurusan'], $status_alumni
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data siswa berhasil ditambahkan!");
        header("Location: ../siswa.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        $status_alumni = isset($_POST['status_alumni']) ? 1 : 0;
        $stmt = mysqli_prepare($koneksi,
            "UPDATE siswa SET nisn=?, nama=?, kelas=?, jurusan=?, status_alumni=? WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, 'ssssii',
            $_POST['nisn'], $_POST['nama'], $_POST['kelas'], $_POST['jurusan'],
            $status_alumni, $_POST['id']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data siswa berhasil diperbarui!");
        header("Location: ../siswa.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM siswa WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data siswa berhasil dihapus.");
        header("Location: ../siswa.php?msg=$msg&type=success");

    } else {
        header("Location: ../siswa.php");
    }
} catch (Exception $e) {
    $msg = urlencode("Gagal: " . $e->getMessage());
    header("Location: ../siswa.php?msg=$msg&type=danger");
}
exit;
