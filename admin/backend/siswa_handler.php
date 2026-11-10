<?php
/**
 * Handler CRUD — Data Siswa
 */

require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

function redirectSiswa(string $message, string $type = 'success'): void
{
    redirectWithMessage('../siswa.php', $message, $type);
}

$action = $_POST['action'] ?? '';

try {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    if ($action === 'tambah') {
        $nisn = validateString($_POST['nisn'] ?? null, 'NISN', 10, 10);
        $nama_siswa = validateString($_POST['nama_siswa'] ?? null, 'Nama siswa', 1, 150);
        $kelas = validateEnum($_POST['kelas'] ?? null, SiswaRepository::getClassOptions(), 'kelas');
        $jurusan = validateEnum($_POST['jurusan'] ?? null, SiswaRepository::getMajorOptions(), 'jurusan');
        $status_alumni = isset($_POST['status_alumni'])
            ? (validateEnum($_POST['status_alumni'], ['1'], 'status alumni') === '1' ? 1 : 0)
            : 0;

        if (!preg_match('/^[0-9]{10}$/D', $nisn)) {
            throw new InvalidArgumentException('NISN harus terdiri dari tepat 10 digit angka.');
        }

        if (SiswaRepository::findByNisn($koneksi, $nisn)) {
            throw new Exception('NISN tersebut sudah terdaftar.');
        }

        if (!SiswaRepository::create($koneksi, [
            'nisn' => $nisn,
            'nama_siswa' => $nama_siswa,
            'kelas' => $kelas,
            'jurusan' => $jurusan,
            'status_alumni' => $status_alumni,
        ])) {
            throw new Exception('Gagal menyimpan data siswa.');
        }

        redirectSiswa('Data siswa berhasil ditambahkan.');
    }

    if ($action === 'edit') {
        $id = validateInteger($_POST['id'] ?? null, 'ID siswa', 1, 2147483647);
        $nisn = validateString($_POST['nisn'] ?? null, 'NISN', 10, 10);
        $nama_siswa = validateString($_POST['nama_siswa'] ?? null, 'Nama siswa', 1, 150);
        $kelas = validateEnum($_POST['kelas'] ?? null, SiswaRepository::getClassOptions(), 'kelas');
        $jurusan = validateEnum($_POST['jurusan'] ?? null, SiswaRepository::getMajorOptions(), 'jurusan');
        $status_alumni = isset($_POST['status_alumni'])
            ? (validateEnum($_POST['status_alumni'], ['1'], 'status alumni') === '1' ? 1 : 0)
            : 0;

        if (!preg_match('/^[0-9]{10}$/D', $nisn)) {
            throw new InvalidArgumentException('NISN harus terdiri dari tepat 10 digit angka.');
        }

        if (SiswaRepository::findByNisnExcept($koneksi, $nisn, $id)) {
            throw new Exception('NISN tersebut sudah digunakan siswa lain.');
        }

        if (!SiswaRepository::update($koneksi, $id, [
            'nisn' => $nisn,
            'nama_siswa' => $nama_siswa,
            'kelas' => $kelas,
            'jurusan' => $jurusan,
            'status_alumni' => $status_alumni,
        ])) {
            throw new Exception('Gagal memperbarui data siswa.');
        }

        redirectSiswa('Data siswa berhasil diperbarui.');
    }

    if ($action === 'hapus') {
        $id = validateInteger($_POST['id'] ?? null, 'ID siswa', 1, 2147483647);

        if (!SiswaRepository::delete($koneksi, $id)) {
            throw new Exception('Gagal menghapus data siswa.');
        }

        redirectSiswa('Data siswa berhasil dihapus.');
    }

    throw new InvalidArgumentException('Aksi siswa tidak valid.');
} catch (Throwable $e) {
    redirectSiswa('Gagal: ' . $e->getMessage(), 'danger');
}
?>
