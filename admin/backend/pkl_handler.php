<?php
/**
 * Handler CRUD — Penempatan PKL
 */

session_start();

require_once '../../backend/connection.php';
require_once '../../backend/repositories/bootstrap.php';

/* =========================
   CEK LOGIN ADMIN
========================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_admin.php");
    exit;
}

/* =========================
   CEK KONEKSI
========================= */

if (!$koneksi) {
    die("Koneksi database gagal.");
}

$action = $_POST['action'] ?? '';

try {

    /* =====================================================
       TAMBAH DATA PKL
    ===================================================== */

    if ($action === 'tambah') {

        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
        $pembimbing = trim($_POST['pembimbing'] ?? '');
        $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
        $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
        $status_penempatan = trim($_POST['status_penempatan'] ?? 'Draft');

        /* VALIDASI */

        if (
            $nama_siswa === '' ||
            $nama_perusahaan === '' ||
            $pembimbing === '' ||
            $tanggal_mulai === '' ||
            $tanggal_selesai === ''
        ) {
            throw new Exception("Semua data PKL wajib diisi.");
        }

        /* =========================
           CARI ID SISWA
        ========================= */

        $siswa = SiswaRepository::findByName($koneksi, $nama_siswa);

        if (!$siswa) {
            throw new Exception(
                "Siswa '$nama_siswa' tidak ditemukan."
            );
        }

        $siswa_id = (int)$siswa['id'];


        /* =========================
           CARI ID PERUSAHAAN
        ========================= */

        $perusahaan = PerusahaanRepository::findByName($koneksi, $nama_perusahaan);

        if (!$perusahaan) {
            throw new Exception(
                "Perusahaan '$nama_perusahaan' tidak ditemukan."
            );
        }

        $perusahaan_id = (int)$perusahaan['id'];


        /* =========================
           INSERT
        ========================= */

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

        header(
            "Location: ../pkl.php?msg=" .
            urlencode("Data PKL berhasil ditambahkan!") .
            "&type=success"
        );
        exit;
    }


    /* =====================================================
       EDIT DATA PKL
    ===================================================== */

    elseif ($action === 'edit') {

        $id = (int)($_POST['id'] ?? 0);

        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
        $pembimbing = trim($_POST['pembimbing'] ?? '');
        $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
        $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
        $status_penempatan = trim($_POST['status_penempatan'] ?? 'Draft');

        if ($id <= 0) {
            throw new Exception("ID PKL tidak valid.");
        }

        if (
            $nama_siswa === '' ||
            $nama_perusahaan === '' ||
            $pembimbing === '' ||
            $tanggal_mulai === '' ||
            $tanggal_selesai === ''
        ) {
            throw new Exception("Semua data PKL wajib diisi.");
        }


        /* =========================
           CARI SISWA
        ========================= */

        $siswa = SiswaRepository::findByName($koneksi, $nama_siswa);

        if (!$siswa) {
            throw new Exception(
                "Siswa '$nama_siswa' tidak ditemukan."
            );
        }

        $siswa_id = (int)$siswa['id'];


        /* =========================
           CARI PERUSAHAAN
        ========================= */

        $perusahaan = PerusahaanRepository::findByName($koneksi, $nama_perusahaan);

        if (!$perusahaan) {
            throw new Exception(
                "Perusahaan '$nama_perusahaan' tidak ditemukan."
            );
        }

        $perusahaan_id = (int)$perusahaan['id'];


        /* =========================
           UPDATE
        ========================= */

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

        header(
            "Location: ../pkl.php?msg=" .
            urlencode("Data PKL berhasil diperbarui!") .
            "&type=success"
        );
        exit;
    }


    /* =====================================================
       HAPUS DATA PKL
    ===================================================== */

    elseif ($action === 'hapus') {

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("ID PKL tidak valid.");
        }

        if (!PklRepository::delete($koneksi, $id)) {
            throw new Exception('Gagal menghapus data PKL.');
        }

        header(
            "Location: ../pkl.php?msg=" .
            urlencode("Data PKL berhasil dihapus.") .
            "&type=success"
        );
        exit;
    }


    /* =====================================================
       ACTION TIDAK DIKENAL
    ===================================================== */

    else {

        header("Location: ../pkl.php");
        exit;
    }


} catch (Exception $e) {

    header(
        "Location: ../pkl.php?msg=" .
        urlencode("Gagal: " . $e->getMessage()) .
        "&type=danger"
    );

    exit;
}
?>
