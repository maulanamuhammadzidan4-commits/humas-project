<?php
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

function redirect_lowongan(string $message, string $type = 'success'): void
{
    redirectWithMessage('../lowongan.php', $message, $type);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException('Akses tidak valid.');
    }

    verifyCsrfToken($_POST['csrf_token'] ?? null);
    $action = validateEnum($_POST['action'] ?? null, ['tambah', 'edit', 'hapus'], 'aksi');

    if ($action === 'hapus') {
        $id = validateInteger($_POST['id'] ?? null, 'ID lowongan', 1, 2147483647);
        if (LowonganRepository::delete($koneksi, $id) === 0) {
            throw new InvalidArgumentException('Data lowongan tidak ditemukan.');
        }
        redirect_lowongan('Lowongan kerja berhasil dihapus.');
    }

    $id = $action === 'edit'
        ? validateInteger($_POST['id'] ?? null, 'ID lowongan', 1, 2147483647)
        : null;
    $perusahaanId = validateInteger($_POST['perusahaan_id'] ?? null, 'Perusahaan', 1, 2147483647);
    $judulPosisi = validateString($_POST['judul_posisi'] ?? null, 'Judul posisi', 1, 100);
    $deskripsi = validateString($_POST['deskripsi_pekerjaan'] ?? null, 'Deskripsi pekerjaan', 1, 10000);
    $kuota = validateInteger($_POST['kuota'] ?? null, 'Kuota', 1, 2147483647);
    $batasPendaftaran = validateDate($_POST['batas_pendaftaran'] ?? null, 'batas pendaftaran');
    $status = validateEnum($_POST['status_loker'] ?? null, ['Buka', 'Tutup'], 'status lowongan');

    if (!PerusahaanRepository::findById($koneksi, $perusahaanId)) {
        throw new InvalidArgumentException('Perusahaan yang dipilih tidak ditemukan.');
    }
    if ($action === 'tambah' && $batasPendaftaran < date('Y-m-d')) {
        throw new InvalidArgumentException('Batas pendaftaran tidak boleh berada di masa lampau.');
    }

    $data = [
        'perusahaan_id' => $perusahaanId,
        'judul_posisi' => $judulPosisi,
        'deskripsi_pekerjaan' => $deskripsi,
        'kuota' => $kuota,
        'batas_pendaftaran' => $batasPendaftaran,
        'status_loker' => $status,
    ];

    $saved = $action === 'tambah'
        ? LowonganRepository::create($koneksi, $data)
        : LowonganRepository::update($koneksi, $id, $data);

    if (!$saved) {
        throw new RuntimeException($action === 'tambah' ? 'Gagal menambahkan lowongan.' : 'Gagal memperbarui lowongan.');
    }

    redirect_lowongan($action === 'tambah'
        ? 'Lowongan kerja berhasil ditambahkan.'
        : 'Lowongan kerja berhasil diperbarui.');
} catch (InvalidArgumentException $e) {
    redirect_lowongan('Gagal: ' . $e->getMessage(), 'danger');
} catch (Throwable $e) {
    error_log('Lowongan handler error: ' . $e->getMessage());
    redirect_lowongan('Terjadi kesalahan saat memproses lowongan.', 'danger');
}
