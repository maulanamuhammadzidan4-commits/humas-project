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
    if ($action === 'tambah') {
        $nisn = trim($_POST['nisn'] ?? '');
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $kelas = trim($_POST['kelas'] ?? '');
        $jurusan = trim($_POST['jurusan'] ?? '');
        $status_alumni = isset($_POST['status_alumni']) ? 1 : 0;

        requireNonEmptyFields([$nisn, $nama_siswa, $kelas, $jurusan], 'Semua data siswa wajib diisi.');

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
        $id = (int) ($_POST['id'] ?? 0);
        $nisn = trim($_POST['nisn'] ?? '');
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $kelas = trim($_POST['kelas'] ?? '');
        $jurusan = trim($_POST['jurusan'] ?? '');
        $status_alumni = isset($_POST['status_alumni']) ? 1 : 0;

        if ($id <= 0) {
            throw new InvalidArgumentException('ID siswa tidak valid.');
        }

        requireNonEmptyFields([$nisn, $nama_siswa, $kelas, $jurusan], 'Semua data siswa wajib diisi.');

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
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new InvalidArgumentException('ID siswa tidak valid.');
        }

        if (!SiswaRepository::delete($koneksi, $id)) {
            throw new Exception('Gagal menghapus data siswa.');
        }

        redirectSiswa('Data siswa berhasil dihapus.');
    }

    header('Location: ../siswa.php');
    exit;
} catch (Throwable $e) {
    redirectSiswa('Gagal: ' . $e->getMessage(), 'danger');
}
?>
