<?php
/**
 * Handler CRUD — Tracer Study
 */
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

function redirectTracer(string $message, string $type = 'success'): void
{
    redirectWithMessage('../tracer.php', $message, $type);
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'tambah') {
        $pendapatan = !empty($_POST['pendapatan_bulanan']) ? (int) $_POST['pendapatan_bulanan'] : null;
        $nama_instansi = !empty($_POST['nama_instansi']) ? trim($_POST['nama_instansi']) : null;

        TracerRepository::create($koneksi, [
            'siswa_id' => (int) ($_POST['id_siswa'] ?? 0),
            'tahun_lulus' => (int) ($_POST['tahun_lulus'] ?? 0),
            'status_alumni' => $_POST['status_alumni'] ?? null,
            'nama_instansi' => $nama_instansi,
            'pendapatan_bulanan' => $pendapatan,
        ]);

        redirectTracer('Data tracer study berhasil ditambahkan!');
    }

    if ($action === 'edit') {
        $pendapatan = !empty($_POST['pendapatan_bulanan']) ? (int) $_POST['pendapatan_bulanan'] : null;
        $nama_instansi = !empty($_POST['nama_instansi']) ? trim($_POST['nama_instansi']) : null;

        TracerRepository::update($koneksi, (int) ($_POST['id'] ?? 0), [
            'siswa_id' => (int) ($_POST['id_siswa'] ?? 0),
            'tahun_lulus' => (int) ($_POST['tahun_lulus'] ?? 0),
            'status_alumni' => $_POST['status_alumni'] ?? null,
            'nama_instansi' => $nama_instansi,
            'pendapatan_bulanan' => $pendapatan,
        ]);

        redirectTracer('Data tracer study berhasil diperbarui!');
    }

    if ($action === 'hapus') {
        TracerRepository::delete($koneksi, (int) ($_POST['id'] ?? 0));
        redirectTracer('Data tracer study berhasil dihapus.');
    }

    header('Location: ../tracer.php');
    exit;
} catch (Throwable $e) {
    redirectTracer('Gagal: ' . $e->getMessage(), 'danger');
}
exit;
