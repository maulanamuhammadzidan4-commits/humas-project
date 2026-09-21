<?php
/**
 * Handler CRUD — Tracer Study
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
        $pendapatan = !empty($_POST['pendapatan_bulanan']) ? (int)$_POST['pendapatan_bulanan'] : null;
        $nama_instansi = !empty($_POST['nama_instansi']) ? $_POST['nama_instansi'] : null;
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO tracer_study (id_siswa, tahun_lulus, status_alumni, nama_instansi, pendapatan_bulanan)
             VALUES (?,?,?,?,?)"
        );
        mysqli_stmt_bind_param($stmt, 'iissi',
            $_POST['id_siswa'], $_POST['tahun_lulus'], $_POST['status_alumni'],
            $nama_instansi, $pendapatan
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data tracer study berhasil ditambahkan!");
        header("Location: ../tracer.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        $pendapatan = !empty($_POST['pendapatan_bulanan']) ? (int)$_POST['pendapatan_bulanan'] : null;
        $nama_instansi = !empty($_POST['nama_instansi']) ? $_POST['nama_instansi'] : null;
        $stmt = mysqli_prepare($koneksi,
            "UPDATE tracer_study SET id_siswa=?, tahun_lulus=?, status_alumni=?, nama_instansi=?, pendapatan_bulanan=?
             WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, 'iissii',
            $_POST['id_siswa'], $_POST['tahun_lulus'], $_POST['status_alumni'],
            $nama_instansi, $pendapatan, $_POST['id']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data tracer study berhasil diperbarui!");
        header("Location: ../tracer.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM tracer_study WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data tracer study berhasil dihapus.");
        header("Location: ../tracer.php?msg=$msg&type=success");

    } else {
        header("Location: ../tracer.php");
    }
} catch (Exception $e) {
    $msg = urlencode("Gagal: " . $e->getMessage());
    header("Location: ../tracer.php?msg=$msg&type=danger");
}
exit;
