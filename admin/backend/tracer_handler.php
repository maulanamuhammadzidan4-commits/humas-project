<?php
/**
 * Handler CRUD — Tracer Study
 */
session_start();
require_once '../../backend/connection.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

$action = $_POST['action'] ?? '';

try {
    if ($action === 'tambah') {
        $pendapatan = !empty($_POST['pendapatan_bulanan']) ? (int)$_POST['pendapatan_bulanan'] : null;
        $nama_instansi = !empty($_POST['nama_instansi']) ? $_POST['nama_instansi'] : null;
        TracerRepository::create($koneksi, [
            'siswa_id' => (int)$_POST['id_siswa'],
            'tahun_lulus' => (int)$_POST['tahun_lulus'],
            'status_alumni' => $_POST['status_alumni'],
            'nama_instansi' => $nama_instansi,
            'pendapatan_bulanan' => $pendapatan,
        ]);
        $msg = urlencode("Data tracer study berhasil ditambahkan!");
        header("Location: ../tracer.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        $pendapatan = !empty($_POST['pendapatan_bulanan']) ? (int)$_POST['pendapatan_bulanan'] : null;
        $nama_instansi = !empty($_POST['nama_instansi']) ? $_POST['nama_instansi'] : null;
        TracerRepository::update($koneksi, (int)$_POST['id'], [
            'siswa_id' => (int)$_POST['id_siswa'],
            'tahun_lulus' => (int)$_POST['tahun_lulus'],
            'status_alumni' => $_POST['status_alumni'],
            'nama_instansi' => $nama_instansi,
            'pendapatan_bulanan' => $pendapatan,
        ]);
        $msg = urlencode("Data tracer study berhasil diperbarui!");
        header("Location: ../tracer.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        TracerRepository::delete($koneksi, (int)$_POST['id']);
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
