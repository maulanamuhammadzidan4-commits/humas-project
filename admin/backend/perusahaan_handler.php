<?php
session_start();
require_once '../../backend/connection.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

$action = $_POST['action'] ?? '';

try {
    $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
    $sektor_bidang = trim($_POST['sektor_bidang'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $penanggung_jawab = trim($_POST['penanggung_jawab'] ?? '');
    $no_telepon = trim($_POST['no_telepon'] ?? '');
    $status_mou = trim($_POST['status_mou'] ?? '');
    $perusahaan_id = (int)($_POST['id'] ?? 0);

    if ($action !== 'hapus' && ($nama_perusahaan === '' || $sektor_bidang === '' || $jurusan === '' || $alamat === '' || $penanggung_jawab === '' || $no_telepon === '' || $status_mou === '')) {
        throw new Exception('Semua data perusahaan wajib diisi.');
    }

    if ($action === 'tambah') {
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO perusahaan (nama_perusahaan, sektor_bidang, jurusan, alamat, penanggung_jawab, no_telepon, status_mou)
             VALUES (?,?,?,?,?,?,?)"
        );
        mysqli_stmt_bind_param($stmt, 'sssssss',
            $nama_perusahaan, $sektor_bidang, $jurusan, $alamat,
            $penanggung_jawab, $no_telepon, $status_mou
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Perusahaan berhasil ditambahkan!");
        header("Location: ../perusahaan.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        $stmt = mysqli_prepare($koneksi,
            "UPDATE perusahaan SET nama_perusahaan=?, sektor_bidang=?, jurusan=?, alamat=?, penanggung_jawab=?, no_telepon=?, status_mou=?
             WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, 'sssssssi',
            $nama_perusahaan, $sektor_bidang, $jurusan, $alamat,
            $penanggung_jawab, $no_telepon, $status_mou, $perusahaan_id
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
