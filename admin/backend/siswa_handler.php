<?php
/**
 * Handler CRUD — Data Siswa
 */

session_start();

require_once '../../backend/connection.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

/* =========================
   CEK KONEKSI DATABASE
========================= */
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/* =========================
   AMBIL ACTION
========================= */
$action = $_POST['action'] ?? '';

try {

    /* =====================================================
       TAMBAH DATA SISWA
    ===================================================== */
    if ($action === 'tambah') {

        $nisn       = trim($_POST['nisn'] ?? '');
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $kelas      = trim($_POST['kelas'] ?? '');
        $jurusan    = trim($_POST['jurusan'] ?? '');

        $status_alumni = isset($_POST['status_alumni']) ? 1 : 0;

        /* =========================
           VALIDASI
        ========================= */
        if (
            $nisn === '' ||
            $nama_siswa === '' ||
            $kelas === '' ||
            $jurusan === ''
        ) {
            throw new Exception("Semua data siswa wajib diisi.");
        }

        /* =========================
           CEK NISN
        ========================= */
        if (SiswaRepository::findByNisn($koneksi, $nisn)) {
            throw new Exception(
                "NISN tersebut sudah terdaftar."
            );
        }

        /* =========================
           INSERT DATA
        ========================= */
        if (!SiswaRepository::create($koneksi, [
            'nisn' => $nisn,
            'nama_siswa' => $nama_siswa,
            'kelas' => $kelas,
            'jurusan' => $jurusan,
            'status_alumni' => $status_alumni,
        ])) {
            throw new Exception('Gagal menyimpan data siswa.');
        }

        header(
            "Location: ../siswa.php?msg=" .
            urlencode("Data siswa berhasil ditambahkan.") .
            "&type=success"
        );

        exit;
    }


    /* =====================================================
       EDIT DATA SISWA
    ===================================================== */
    elseif ($action === 'edit') {

        $id = (int)($_POST['id'] ?? 0);

        $nisn       = trim($_POST['nisn'] ?? '');
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $kelas      = trim($_POST['kelas'] ?? '');
        $jurusan    = trim($_POST['jurusan'] ?? '');

        $status_alumni = isset($_POST['status_alumni']) ? 1 : 0;

        /* =========================
           VALIDASI ID
        ========================= */
        if ($id <= 0) {

            throw new Exception(
                "ID siswa tidak valid."
            );
        }

        /* =========================
           VALIDASI DATA
        ========================= */
        if (
            $nisn === '' ||
            $nama_siswa === '' ||
            $kelas === '' ||
            $jurusan === ''
        ) {

            throw new Exception(
                "Semua data siswa wajib diisi."
            );
        }

        /* =========================
           CEK NISN
           AGAR TIDAK SAMA DENGAN SISWA LAIN
        ========================= */
        if (SiswaRepository::findByNisnExcept($koneksi, $nisn, $id)) {
            throw new Exception(
                "NISN tersebut sudah digunakan siswa lain."
            );
        }

        /* =========================
           UPDATE DATA
        ========================= */
        if (!SiswaRepository::update($koneksi, $id, [
            'nisn' => $nisn,
            'nama_siswa' => $nama_siswa,
            'kelas' => $kelas,
            'jurusan' => $jurusan,
            'status_alumni' => $status_alumni,
        ])) {
            throw new Exception('Gagal memperbarui data siswa.');
        }

        header(
            "Location: ../siswa.php?msg=" .
            urlencode("Data siswa berhasil diperbarui.") .
            "&type=success"
        );

        exit;
    }


    /* =====================================================
       HAPUS DATA SISWA
    ===================================================== */
    elseif ($action === 'hapus') {

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {

            throw new Exception(
                "ID siswa tidak valid."
            );
        }

        /* =========================
           DELETE DATA
        ========================= */
        if (!SiswaRepository::delete($koneksi, $id)) {
            throw new Exception('Gagal menghapus data siswa.');
        }

        header(
            "Location: ../siswa.php?msg=" .
            urlencode("Data siswa berhasil dihapus.") .
            "&type=success"
        );

        exit;
    }


    /* =====================================================
       ACTION TIDAK DIKENAL
    ===================================================== */
    else {

        header(
            "Location: ../siswa.php"
        );

        exit;
    }


} catch (Exception $e) {

    header(
        "Location: ../siswa.php?msg=" .
        urlencode("Gagal: " . $e->getMessage()) .
        "&type=danger"
    );

    exit;
}
?>
