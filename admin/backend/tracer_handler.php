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

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException('Akses tidak valid.');
    }

    verifyCsrfToken($_POST['csrf_token'] ?? null);
    $action = validateEnum($_POST['action'] ?? null, ['tambah', 'edit', 'hapus'], 'aksi');

    if ($action === 'hapus') {
        $id = validateInteger($_POST['id'] ?? null, 'ID tracer', 1, 2147483647);
        if (!TracerRepository::delete($koneksi, $id)) {
            throw new InvalidArgumentException('Data tracer tidak ditemukan.');
        }
        redirectTracer('Data tracer study berhasil dihapus.');
    }

    $id = $action === 'edit'
        ? validateInteger($_POST['id'] ?? null, 'ID tracer', 1, 2147483647)
        : 0;
    $namaSiswa = validateString($_POST['nama_siswa'] ?? null, 'Nama siswa', 1, 150);
    $siswa = SiswaRepository::findUniqueByName($koneksi, $namaSiswa);
    if (!$siswa) {
        throw new InvalidArgumentException('Nama siswa tidak ditemukan di data siswa.');
    }

    $siswaId = (int) $siswa['id'];
    if (TracerRepository::findByStudentId($koneksi, $siswaId, $id)) {
        throw new InvalidArgumentException('Siswa tersebut sudah memiliki data tracer study.');
    }

    $tahunLulus = validateInteger($_POST['tahun_lulus'] ?? null, 'Tahun lulus', 1901, (int) date('Y'));
    $statusAlumni = validateEnum(
        $_POST['status_alumni'] ?? null,
        ['Bekerja', 'Kuliah', 'Wirausaha', 'Mencari Kerja', 'menikah'],
        'status alumni'
    );

    $namaInstansi = $_POST['nama_instansi'] ?? '';
    if (!is_string($namaInstansi)) {
        throw new InvalidArgumentException('Nama instansi tidak valid.');
    }
    $namaInstansi = trim($namaInstansi) === ''
        ? null
        : validateString($namaInstansi, 'Nama instansi', 1, 150);

    $pendapatanInput = $_POST['pendapatan_bulanan'] ?? '';
    $pendapatan = $pendapatanInput === ''
        ? null
        : validateInteger($pendapatanInput, 'Pendapatan bulanan', 0, 2147483647);

    $data = [
        'siswa_id' => $siswaId,
        'tahun_lulus' => $tahunLulus,
        'status_alumni' => $statusAlumni,
        'nama_instansi' => $namaInstansi,
        'pendapatan_bulanan' => $pendapatan,
    ];

    $saved = $action === 'tambah'
        ? TracerRepository::create($koneksi, $data)
        : TracerRepository::update($koneksi, $id, $data);
    if (!$saved) {
        throw new RuntimeException($action === 'tambah'
            ? 'Gagal menyimpan data tracer study.'
            : 'Gagal memperbarui data tracer study.');
    }

    redirectTracer($action === 'tambah'
        ? 'Data tracer study berhasil ditambahkan!'
        : 'Data tracer study berhasil diperbarui!');
} catch (InvalidArgumentException $e) {
    redirectTracer('Gagal: ' . $e->getMessage(), 'danger');
} catch (Throwable $e) {
    error_log('Tracer handler error: ' . $e->getMessage());
    redirectTracer('Terjadi kesalahan saat memproses data tracer study.', 'danger');
}
