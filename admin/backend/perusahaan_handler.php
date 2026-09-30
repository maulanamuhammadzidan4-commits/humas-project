<?php
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

function redirectPerusahaan(string $message, string $type = 'success'): void
{
    redirectWithMessage('../perusahaan.php', $message, $type);
}

$action = $_POST['action'] ?? '';

try {
    $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
    $sektor_bidang = trim($_POST['sektor_bidang'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $penanggung_jawab = trim($_POST['penanggung_jawab'] ?? '');
    $no_telepon = trim($_POST['no_telepon'] ?? '');
    $status_mou = trim($_POST['status_mou'] ?? '');
    $perusahaan_id = (int) ($_POST['id'] ?? 0);

    if ($action !== 'hapus') {
        requireNonEmptyFields([
            $nama_perusahaan,
            $sektor_bidang,
            $jurusan,
            $alamat,
            $penanggung_jawab,
            $no_telepon,
            $status_mou,
        ], 'Semua data perusahaan wajib diisi.');
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

        redirectPerusahaan('Perusahaan berhasil ditambahkan!');
    }

    if ($action === 'edit') {
        if ($perusahaan_id <= 0) {
            throw new InvalidArgumentException('ID perusahaan tidak valid.');
        }

        PerusahaanRepository::update($koneksi, $perusahaan_id, [
            'nama_perusahaan' => $nama_perusahaan,
            'sektor_bidang' => $sektor_bidang,
            'jurusan' => $jurusan,
            'alamat' => $alamat,
            'penanggung_jawab' => $penanggung_jawab,
            'no_telepon' => $no_telepon,
            'status_mou' => $status_mou,
        ]);

        redirectPerusahaan('Data perusahaan berhasil diperbarui!');
    }

    if ($action === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new InvalidArgumentException('ID perusahaan tidak valid.');
        }

        PerusahaanRepository::delete($koneksi, $id);
        redirectPerusahaan('Perusahaan berhasil dihapus.');
    }

    header('Location: ../perusahaan.php');
    exit;
} catch (Throwable $e) {
    redirectPerusahaan('Gagal: ' . $e->getMessage(), 'danger');
}
exit;
