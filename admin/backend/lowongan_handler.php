<?php
session_start();
require_once '../../backend/connection.php';
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
    $cek_perusahaan = mysqli_prepare(
        $koneksi,
        "SELECT id FROM perusahaan WHERE id = ? LIMIT 1"
    );
    if (!$cek_perusahaan) {
        redirect_lowongan(
            'Gagal memeriksa perusahaan: ' .
            mysqli_error($koneksi),
            'error'
        );
    }
    mysqli_stmt_bind_param(
        $cek_perusahaan,
        "i",
        $perusahaan_id
    );
    mysqli_stmt_execute(
        $cek_perusahaan
    );
    $hasil_perusahaan =
        mysqli_stmt_get_result(
            $cek_perusahaan
        );
    if (
        mysqli_num_rows(
            $hasil_perusahaan
        ) === 0
    ) {
        mysqli_stmt_close(
            $cek_perusahaan
        );
        redirect_lowongan(
            'Perusahaan yang dipilih tidak ditemukan.',
            'error'
        );
    }
    mysqli_stmt_close(
        $cek_perusahaan
    );

/*--INSERT--*/
    $sql = "
        INSERT INTO lowongan_kerja
        (
            perusahaan_id,
            judul_posisi,
            deskripsi_pekerjaan,
            kuota,
            batas_pendaftaran,
            status_loker
        )
        VALUES
        (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare(
        $koneksi,
        $sql
    );

    if (!$stmt) {

        redirect_lowongan(
            'Gagal menyiapkan data: ' .
            mysqli_error($koneksi),
            'error'
        );

    }


    mysqli_stmt_bind_param(
        $stmt,
        "ississ",
        $perusahaan_id,
        $judul_posisi,
        $deskripsi_pekerjaan,
        $kuota,
        $batas_pendaftaran,
        $status_loker
    );


    if (
        mysqli_stmt_execute($stmt)
    ) {

        mysqli_stmt_close($stmt);

        redirect_lowongan(
            'Lowongan kerja berhasil ditambahkan.'
        );

    }


    $error =
        mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    redirect_lowongan(
        'Gagal menambahkan lowongan: ' .
        $error,
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

    $sql = "
        UPDATE lowongan_kerja
        SET
            perusahaan_id = ?,
            judul_posisi = ?,
            deskripsi_pekerjaan = ?,
            kuota = ?,
            batas_pendaftaran = ?,
            status_loker = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare(
        $koneksi,
        $sql
    );

    if (!$stmt) {

        redirect_lowongan(
            'Gagal menyiapkan update: ' .
            mysqli_error($koneksi),
            'error'
        );

    }


    mysqli_stmt_bind_param(
        $stmt,
        "ississi",
        $perusahaan_id,
        $judul_posisi,
        $deskripsi_pekerjaan,
        $kuota,
        $batas_pendaftaran,
        $status_loker,
        $id
    );


    if (
        mysqli_stmt_execute($stmt)
    ) {

        mysqli_stmt_close($stmt);

        redirect_lowongan(
            'Lowongan kerja berhasil diperbarui.'
        );

    }


    $error =
        mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    redirect_lowongan(
        'Gagal memperbarui lowongan: ' .
        $error,
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


    $sql = "
        DELETE FROM lowongan_kerja
        WHERE id = ?
    ";


    $stmt = mysqli_prepare(
        $koneksi,
        $sql
    );


    if (!$stmt) {

        redirect_lowongan(
            'Gagal menyiapkan penghapusan: ' .
            mysqli_error($koneksi),
            'error'
        );

    }


    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );


    if (
        mysqli_stmt_execute($stmt)
    ) {

        if (
            mysqli_stmt_affected_rows($stmt) > 0
        ) {

            mysqli_stmt_close($stmt);

            redirect_lowongan(
                'Lowongan kerja berhasil dihapus.'
            );

        }

        mysqli_stmt_close($stmt);

        redirect_lowongan(
            'Data lowongan tidak ditemukan.',
            'error'
        );

    }


    $error =
        mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    redirect_lowongan(
        'Gagal menghapus lowongan: ' .
        $error,
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