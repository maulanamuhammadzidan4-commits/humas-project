<?php
session_start();
require_once '../../backend/connection.php';
require_once '../../backend/repositories/bootstrap.php';
require_once '../includes/auth.php';

/*--FUNGSI REDIRECT--*/
function redirect_lowongan(
    string $message,
    string $type = 'success'
): void {
    header(
        'Location: ../lowongan.php?type=' .
        urlencode($type) .
        '&msg=' .
        urlencode($message)
    );
    exit;
}

/*--CEK REQUEST--*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_lowongan(
        'Akses tidak valid.',
        'error'
    );
}

/*--ACTION--*/
$action = $_POST['action'] ?? '';

/*--TAMBAH--*/
if ($action === 'tambah') {
    $perusahaan_id =
        (int)($_POST['perusahaan_id'] ?? 0);
    $judul_posisi =
        trim($_POST['judul_posisi'] ?? '');
    $deskripsi_pekerjaan =
        trim($_POST['deskripsi_pekerjaan'] ?? '');
    $kuota =
        (int)($_POST['kuota'] ?? 0);
    $batas_pendaftaran =
        trim($_POST['batas_pendaftaran'] ?? '');
    $status_loker =
        trim($_POST['status_loker'] ?? 'Buka');

/*--VALIDASI--*/
    if (
        $perusahaan_id <= 0 ||
        $judul_posisi === '' ||
        $deskripsi_pekerjaan === '' ||
        $kuota <= 0 ||
        $batas_pendaftaran === ''
    ) {
        redirect_lowongan(
            'Semua data wajib diisi dengan benar.',
            'error'
        );
    }

/*--CEK PERUSAHAAN--*/
    if (!PerusahaanRepository::findById($koneksi, $perusahaan_id)) {
        redirect_lowongan(
            'Perusahaan yang dipilih tidak ditemukan.',
            'error'
        );
    }

/*--INSERT--*/
    if (LowonganRepository::create($koneksi, [
        'perusahaan_id' => $perusahaan_id,
        'judul_posisi' => $judul_posisi,
        'deskripsi_pekerjaan' => $deskripsi_pekerjaan,
        'kuota' => $kuota,
        'batas_pendaftaran' => $batas_pendaftaran,
        'status_loker' => $status_loker,
    ])) {
        redirect_lowongan(
            'Lowongan kerja berhasil ditambahkan.'
        );
    }

    redirect_lowongan(
        'Gagal menambahkan lowongan.',
        'error'
    );

}


/*
|--------------------------------------------------------------------------
| EDIT
|--------------------------------------------------------------------------
*/

if ($action === 'edit') {

    $id =
        (int)($_POST['id'] ?? 0);

    $perusahaan_id =
        (int)($_POST['perusahaan_id'] ?? 0);

    $judul_posisi =
        trim($_POST['judul_posisi'] ?? '');

    $deskripsi_pekerjaan =
        trim($_POST['deskripsi_pekerjaan'] ?? '');

    $kuota =
        (int)($_POST['kuota'] ?? 0);

    $batas_pendaftaran =
        trim($_POST['batas_pendaftaran'] ?? '');

    $status_loker =
        trim($_POST['status_loker'] ?? 'Buka');


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if (
        $id <= 0 ||
        $perusahaan_id <= 0 ||
        $judul_posisi === '' ||
        $deskripsi_pekerjaan === '' ||
        $kuota <= 0 ||
        $batas_pendaftaran === ''
    ) {

        redirect_lowongan(
            'Data edit tidak lengkap.',
            'error'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    if (LowonganRepository::update($koneksi, $id, [
        'perusahaan_id' => $perusahaan_id,
        'judul_posisi' => $judul_posisi,
        'deskripsi_pekerjaan' => $deskripsi_pekerjaan,
        'kuota' => $kuota,
        'batas_pendaftaran' => $batas_pendaftaran,
        'status_loker' => $status_loker,
    ])) {
        redirect_lowongan(
            'Lowongan kerja berhasil diperbarui.'
        );
    }

    redirect_lowongan(
        'Gagal memperbarui lowongan.',
        'error'
    );

}


/*
|--------------------------------------------------------------------------
| HAPUS
|--------------------------------------------------------------------------
*/

if ($action === 'hapus') {

    $id =
        (int)($_POST['id'] ?? 0);


    if ($id <= 0) {

        redirect_lowongan(
            'ID lowongan tidak valid.',
            'error'
        );

    }


    if (LowonganRepository::delete($koneksi, $id) > 0) {
        redirect_lowongan('Lowongan kerja berhasil dihapus.');
    }

    redirect_lowongan(
        'Data lowongan tidak ditemukan.',
        'error'
    );

}


/*
|--------------------------------------------------------------------------
| ACTION TIDAK DIKENAL
|--------------------------------------------------------------------------
*/

redirect_lowongan(
    'Aksi tidak dikenali.',
    'error'
);