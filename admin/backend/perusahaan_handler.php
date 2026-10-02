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
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    if ($action !== 'hapus') {
        $nama_perusahaan = validateString($_POST['nama_perusahaan'] ?? null, 'Nama perusahaan', 1, 150);
        $sektor_bidang = validateString($_POST['sektor_bidang'] ?? null, 'Sektor bidang', 1, 100);
        $jurusan = validateString($_POST['jurusan'] ?? null, 'Jurusan', 1, 5000);
        $alamat = validateString($_POST['alamat'] ?? null, 'Alamat', 1, 5000);
        $penanggung_jawab = validateString($_POST['penanggung_jawab'] ?? null, 'Penanggung jawab', 1, 100);
        $no_telepon = validateString($_POST['no_telepon'] ?? null, 'Nomor telepon', 1, 20);
        $jumlahDigitTelepon = strlen((string) preg_replace('/\D/', '', $no_telepon));
        if (
            !preg_match('/^\+?[0-9][0-9() .-]*[0-9]$/D', $no_telepon)
            || $jumlahDigitTelepon < 5
            || $jumlahDigitTelepon > 15
        ) {
            throw new InvalidArgumentException('Nomor telepon harus berisi 5 sampai 15 digit dan hanya menggunakan format telepon yang valid.');
        }
        $status_mou = validateEnum($_POST['status_mou'] ?? null, ['Proses', 'Aktif', 'Kadaluarsa'], 'status MoU');
    }

    if ($action === 'tambah') {
        if (!PerusahaanRepository::create($koneksi, [
            'nama_perusahaan' => $nama_perusahaan,
            'sektor_bidang' => $sektor_bidang,
            'jurusan' => $jurusan,
            'alamat' => $alamat,
            'penanggung_jawab' => $penanggung_jawab,
            'no_telepon' => $no_telepon,
            'status_mou' => $status_mou,
        ])) {
            throw new RuntimeException('Gagal menyimpan data perusahaan.');
        }

        redirectPerusahaan('Perusahaan berhasil ditambahkan!');
    }

    if ($action === 'edit') {
        $perusahaan_id = validateInteger($_POST['id'] ?? null, 'ID perusahaan', 1, 2147483647);

        if (!PerusahaanRepository::update($koneksi, $perusahaan_id, [
            'nama_perusahaan' => $nama_perusahaan,
            'sektor_bidang' => $sektor_bidang,
            'jurusan' => $jurusan,
            'alamat' => $alamat,
            'penanggung_jawab' => $penanggung_jawab,
            'no_telepon' => $no_telepon,
            'status_mou' => $status_mou,
        ])) {
            throw new RuntimeException('Gagal memperbarui data perusahaan.');
        }

        redirectPerusahaan('Data perusahaan berhasil diperbarui!');
    }

    if ($action === 'hapus') {
        $id = validateInteger($_POST['id'] ?? null, 'ID perusahaan', 1, 2147483647);

        if (!PerusahaanRepository::delete($koneksi, $id)) {
            throw new RuntimeException('Gagal menghapus data perusahaan.');
        }
        redirectPerusahaan('Perusahaan berhasil dihapus.');
    }

    throw new InvalidArgumentException('Aksi perusahaan tidak valid.');
} catch (Throwable $e) {
    redirectPerusahaan('Gagal: ' . $e->getMessage(), 'danger');
}
exit;
