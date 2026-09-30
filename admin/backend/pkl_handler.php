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
    if ($action === 'tambah') {
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
        $pembimbing = trim($_POST['pembimbing'] ?? '');
        $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
        $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
        $status_penempatan = trim($_POST['status_penempatan'] ?? 'Draft');

        requireNonEmptyFields([
            $nama_siswa,
            $nama_perusahaan,
            $pembimbing,
            $tanggal_mulai,
            $tanggal_selesai,
        ], 'Semua data PKL wajib diisi.');

        $siswa_id = resolveEntityId(
            fn() => SiswaRepository::findByName($koneksi, $nama_siswa),
            'Siswa',
            $nama_siswa
        );

        $perusahaan_id = resolveEntityId(
            fn() => PerusahaanRepository::findByName($koneksi, $nama_perusahaan),
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
        $id = (int) ($_POST['id'] ?? 0);
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
        $pembimbing = trim($_POST['pembimbing'] ?? '');
        $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
        $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
        $status_penempatan = trim($_POST['status_penempatan'] ?? 'Draft');

        if ($id <= 0) {
            throw new InvalidArgumentException('ID PKL tidak valid.');
        }

        requireNonEmptyFields([
            $nama_siswa,
            $nama_perusahaan,
            $pembimbing,
            $tanggal_mulai,
            $tanggal_selesai,
        ], 'Semua data PKL wajib diisi.');

        $siswa_id = resolveEntityId(
            fn() => SiswaRepository::findByName($koneksi, $nama_siswa),
            'Siswa',
            $nama_siswa
        );

        $perusahaan_id = resolveEntityId(
            fn() => PerusahaanRepository::findByName($koneksi, $nama_perusahaan),
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
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new InvalidArgumentException('ID PKL tidak valid.');
        }

        if (!PklRepository::delete($koneksi, $id)) {
            throw new Exception('Gagal menghapus data PKL.');
        }

        redirectPkl('Data PKL berhasil dihapus.');
    }

    header('Location: ../pkl.php');
    exit;
} catch (Throwable $e) {
    redirectPkl('Gagal: ' . $e->getMessage(), 'danger');
}
?>
