<?php
/**
 * Handler CRUD — Penempatan PKL
 */

require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

if (!$koneksi) {
    die('Koneksi database gagal.');
}

function redirectPkl(string $message, string $type = 'success'): void
{
    redirectWithMessage('../pkl.php', $message, $type);
}

function resolveEntityId(callable $resolver, string $label, string $name): int
{
    $entity = $resolver();

    if (!$entity) {
        throw new InvalidArgumentException("{$label} '{$name}' tidak ditemukan.");
    }

    return (int) $entity['id'];
}

$action = $_POST['action'] ?? '';

try {
    verifyCsrfToken($_POST['csrf_token'] ?? null);
    $action = validateEnum($_POST['action'] ?? null, ['tambah', 'edit', 'hapus'], 'aksi');

    if ($action === 'tambah') {
        $nama_siswa = validateString($_POST['nama_siswa'] ?? null, 'Nama siswa', 1, 150);
        $nama_perusahaan = validateString($_POST['nama_perusahaan'] ?? null, 'Nama perusahaan', 1, 150);
        $pembimbing = validateString($_POST['pembimbing'] ?? null, 'Nama pembimbing', 1, 100);
        $tanggal_mulai = validateDate($_POST['tanggal_mulai'] ?? null, 'tanggal mulai');
        $tanggal_selesai = validateDate($_POST['tanggal_selesai'] ?? null, 'tanggal selesai');
        $status_penempatan = validateEnum($_POST['status_penempatan'] ?? 'Draft', ['Draft', 'Disetujui', 'Selesai'], 'status penempatan');
        if ($tanggal_selesai < $tanggal_mulai) {
            throw new InvalidArgumentException('Tanggal selesai harus sama atau setelah tanggal mulai.');
        }

        $siswa_id = resolveEntityId(
            fn() => SiswaRepository::findUniqueByName($koneksi, $nama_siswa),
            'Siswa',
            $nama_siswa
        );

        $perusahaan_id = resolveEntityId(
            fn() => PerusahaanRepository::findUniqueByName($koneksi, $nama_perusahaan),
            'Perusahaan',
            $nama_perusahaan
        );

        if (!PklRepository::create($koneksi, [
            'siswa_id' => $siswa_id,
            'perusahaan_id' => $perusahaan_id,
            'pembimbing_guru' => $pembimbing,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'status_penempatan' => $status_penempatan,
        ])) {
            throw new Exception('Gagal menyimpan data PKL.');
        }

        redirectPkl('Data PKL berhasil ditambahkan!');
    }

    if ($action === 'edit') {
        $id = validateInteger($_POST['id'] ?? null, 'ID PKL', 1, 2147483647);
        $nama_siswa = validateString($_POST['nama_siswa'] ?? null, 'Nama siswa', 1, 150);
        $nama_perusahaan = validateString($_POST['nama_perusahaan'] ?? null, 'Nama perusahaan', 1, 150);
        $pembimbing = validateString($_POST['pembimbing'] ?? null, 'Nama pembimbing', 1, 100);
        $tanggal_mulai = validateDate($_POST['tanggal_mulai'] ?? null, 'tanggal mulai');
        $tanggal_selesai = validateDate($_POST['tanggal_selesai'] ?? null, 'tanggal selesai');
        $status_penempatan = validateEnum($_POST['status_penempatan'] ?? 'Draft', ['Draft', 'Disetujui', 'Selesai'], 'status penempatan');
        if ($tanggal_selesai < $tanggal_mulai) {
            throw new InvalidArgumentException('Tanggal selesai harus sama atau setelah tanggal mulai.');
        }

        $siswa_id = resolveEntityId(
            fn() => SiswaRepository::findUniqueByName($koneksi, $nama_siswa),
            'Siswa',
            $nama_siswa
        );

        $perusahaan_id = resolveEntityId(
            fn() => PerusahaanRepository::findUniqueByName($koneksi, $nama_perusahaan),
            'Perusahaan',
            $nama_perusahaan
        );

        if (!PklRepository::update($koneksi, $id, [
            'siswa_id' => $siswa_id,
            'perusahaan_id' => $perusahaan_id,
            'pembimbing_guru' => $pembimbing,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'status_penempatan' => $status_penempatan,
        ])) {
            throw new Exception('Gagal memperbarui data PKL.');
        }

        redirectPkl('Data PKL berhasil diperbarui!');
    }

    if ($action === 'hapus') {
        $id = validateInteger($_POST['id'] ?? null, 'ID PKL', 1, 2147483647);

        if (!PklRepository::delete($koneksi, $id)) {
            throw new Exception('Gagal menghapus data PKL.');
        }

        redirectPkl('Data PKL berhasil dihapus.');
    }

    throw new InvalidArgumentException('Aksi PKL tidak valid.');
} catch (Throwable $e) {
    redirectPkl('Gagal: ' . $e->getMessage(), 'danger');
}
?>
