<?php
session_start();
require_once '../../backend/connection.php';
require_once '../../backend/repositories/bootstrap.php';
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
        PerusahaanRepository::create($koneksi, [
            'nama_perusahaan' => $nama_perusahaan,
            'sektor_bidang' => $sektor_bidang,
            'jurusan' => $jurusan,
            'alamat' => $alamat,
            'penanggung_jawab' => $penanggung_jawab,
            'no_telepon' => $no_telepon,
            'status_mou' => $status_mou,
        ]);
        $msg = urlencode("Perusahaan berhasil ditambahkan!");
        header("Location: ../perusahaan.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        PerusahaanRepository::update($koneksi, $perusahaan_id, [
            'nama_perusahaan' => $nama_perusahaan,
            'sektor_bidang' => $sektor_bidang,
            'jurusan' => $jurusan,
            'alamat' => $alamat,
            'penanggung_jawab' => $penanggung_jawab,
            'no_telepon' => $no_telepon,
            'status_mou' => $status_mou,
        ]);
        $msg = urlencode("Data perusahaan berhasil diperbarui!");
        header("Location: ../perusahaan.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        PerusahaanRepository::delete($koneksi, (int)$_POST['id']);
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
