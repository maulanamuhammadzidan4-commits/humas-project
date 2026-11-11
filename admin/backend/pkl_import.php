
<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../backend/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$halamanKembali = '../pkl.php';

try {

    // ==========================================
    // 1. VALIDASI METODE REQUEST DAN CSRF
    // ==========================================

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Metode permintaan tidak diizinkan.');
    }

    verifyCsrfToken($_POST['csrf_token'] ?? '');

    // ==========================================
    // 2. VALIDASI FILE
    // ==========================================

    if (!isset($_FILES['file_import'])) {
        throw new RuntimeException('File Excel belum dipilih.');
    }

    $file = $_FILES['file_import'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Terjadi kesalahan saat mengunggah file.');
    }

    if ($file['size'] <= 0 || $file['size'] > 10 * 1024 * 1024) {
        throw new RuntimeException('Ukuran file harus maksimal 10 MB.');
    }

    $ekstensi = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    $ekstensiDiizinkan = ['xlsx', 'xls', 'csv'];

    if (!in_array($ekstensi, $ekstensiDiizinkan, true)) {
        throw new RuntimeException(
            'Format file harus XLSX, XLS, atau CSV.'
        );
    }

    // ==========================================
    // 3. MEMBACA FILE EXCEL
    // ==========================================

    $spreadsheet = IOFactory::load($file['tmp_name']);

    $sheet = null;

    foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
        if (strtolower(trim($worksheet->getTitle())) === 'peserta') {
            $sheet = $worksheet;
            break;
        }
    }

    if ($sheet === null) {
        $sheet = $spreadsheet->getActiveSheet();
    }

    $barisTerakhir = $sheet->getHighestDataRow();
    $kolomTerakhir = $sheet->getHighestDataColumn();

    // ==========================================
    // 4. MENCARI BARIS HEADER
    // ==========================================

    $barisHeader = 0;
    $kolom = [];

    for ($baris = 1; $baris <= min(20, $barisTerakhir); $baris++) {

        $header = [];

        foreach (
            $sheet->rangeToArray(
                'A' . $baris . ':' . $kolomTerakhir . $baris,
                null,
                true,
                true,
                false
            )[0] as $index => $nilai
        ) {

            $namaHeader = strtolower(
                preg_replace('/\s+/', ' ', trim((string) $nilai))
            );

            $header[$namaHeader] = $index;
        }

        if (
            isset($header['nisn']) &&
            isset($header['iduka']) &&
            isset($header['guru pembimbing'])
        ) {
            $barisHeader = $baris;
            $kolom = $header;
            break;
        }
    }

    if ($barisHeader === 0) {
        throw new RuntimeException(
            'Header NISN, IDUKA, dan Guru Pembimbing tidak ditemukan.'
        );
    }

    // ==========================================
    // 5. FUNGSI NORMALISASI
    // ==========================================

    $normalisasi = static function ($nilai): string {

        $nilai = trim((string) $nilai);

        $nilai = preg_replace('/\s+/u', ' ', $nilai);

        return function_exists('mb_strtolower')
            ? mb_strtolower($nilai, 'UTF-8')
            : strtolower($nilai);
    };

    // ==========================================
    // 6. MEMUAT DATA PERUSAHAAN
    // ==========================================

    $perusahaanMap = [];

    $queryPerusahaan = mysqli_query(
        $koneksi,
        "SELECT id, nama_perusahaan
         FROM perusahaan"
    );

    while ($perusahaan = mysqli_fetch_assoc($queryPerusahaan)) {

        $namaNormal = $normalisasi(
            $perusahaan['nama_perusahaan']
        );

        if ($namaNormal === '') {
            continue;
        }

        $perusahaanMap[$namaNormal][] = (int) $perusahaan['id'];
    }

    // ==========================================
    // 7. MENYIAPKAN QUERY DATABASE
    // ==========================================

    $stmtSiswa = mysqli_prepare(
        $koneksi,
        "SELECT id
         FROM siswa
         WHERE TRIM(nisn) = ?
         LIMIT 2"
    );

    $stmtCekPenempatan = mysqli_prepare(
        $koneksi,
        "SELECT id
         FROM pkl_penempatan
         WHERE siswa_id = ?
         LIMIT 1"
    );

    $stmtInsert = mysqli_prepare(
        $koneksi,
        "INSERT INTO pkl_penempatan
        (
            siswa_id,
            perusahaan_id,
            pembimbing_guru,
            tanggal_mulai,
            tanggal_selesai,
            status_penempatan
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    // ==========================================
    // 8. PROSES IMPOR
    // ==========================================

    $berhasil = 0;
    $dilewati = 0;
    $gagal = 0;
    $detailGagal = [];

    $tanggalMulai = '2026-11-11';
    $tanggalSelesai = '2027-03-11';
    $status = 'Draft';

    mysqli_begin_transaction($koneksi);

    for (
        $baris = $barisHeader + 1;
        $baris <= $barisTerakhir;
        $baris++
    ) {

        $nilaiBaris = $sheet->rangeToArray(
            'A' . $baris . ':' . $kolomTerakhir . $baris,
            null,
            true,
            true,
            false
        )[0];

        $nisn = trim(
            (string) ($nilaiBaris[$kolom['nisn']] ?? '')
        );

        $namaPerusahaan = trim(
            (string) ($nilaiBaris[$kolom['iduka']] ?? '')
        );

        $guruPembimbing = trim(
            (string) ($nilaiBaris[$kolom['guru pembimbing']] ?? '')
        );

        // Lewati baris kosong
        if (
            $nisn === '' &&
            $namaPerusahaan === '' &&
            $guruPembimbing === ''
        ) {
            continue;
        }

        // Validasi data wajib
        if (
            $nisn === '' ||
            $namaPerusahaan === '' ||
            $guruPembimbing === ''
        ) {

            $dilewati++;

            $detailGagal[] =
                "Baris {$baris}: NISN, IDUKA, atau Guru Pembimbing kosong.";

            continue;
        }

        // --------------------------------------
        // CARI SISWA BERDASARKAN NISN
        // --------------------------------------

        mysqli_stmt_bind_param(
            $stmtSiswa,
            's',
            $nisn
        );

        mysqli_stmt_execute($stmtSiswa);

        $hasilSiswa = mysqli_stmt_get_result($stmtSiswa);

        if (mysqli_num_rows($hasilSiswa) !== 1) {

            $dilewati++;

            $detailGagal[] =
                "Baris {$baris}: NISN {$nisn} tidak ditemukan atau tidak unik.";

            continue;
        }

        $dataSiswa = mysqli_fetch_assoc($hasilSiswa);
        $siswaId = (int) $dataSiswa['id'];

        // --------------------------------------
        // CARI PERUSAHAAN BERDASARKAN IDUKA
        // --------------------------------------

        $namaNormal = $normalisasi($namaPerusahaan);

        $daftarPerusahaan = $perusahaanMap[$namaNormal] ?? [];

        if (count($daftarPerusahaan) !== 1) {

            $dilewati++;

            $detailGagal[] =
                "Baris {$baris}: Perusahaan '{$namaPerusahaan}' tidak ditemukan atau tidak unik.";

            continue;
        }

        $perusahaanId = (int) $daftarPerusahaan[0];

        // --------------------------------------
        // CEK PENEMPATAN PKL SEBELUMNYA
        // --------------------------------------

        mysqli_stmt_bind_param(
            $stmtCekPenempatan,
            'i',
            $siswaId
        );

        mysqli_stmt_execute($stmtCekPenempatan);

        $hasilPenempatan = mysqli_stmt_get_result(
            $stmtCekPenempatan
        );

        if (mysqli_num_rows($hasilPenempatan) > 0) {

            $dilewati++;

            $detailGagal[] =
                "Baris {$baris}: Siswa dengan NISN {$nisn} sudah memiliki penempatan PKL.";

            continue;
        }

        // --------------------------------------
        // SIMPAN PENEMPATAN PKL
        // --------------------------------------

        mysqli_stmt_bind_param(
            $stmtInsert,
            'iissss',
            $siswaId,
            $perusahaanId,
            $guruPembimbing,
            $tanggalMulai,
            $tanggalSelesai,
            $status
        );

        mysqli_stmt_execute($stmtInsert);

        $berhasil++;
    }

    // ==========================================
    // 9. COMMIT TRANSAKSI
    // ==========================================

    mysqli_commit($koneksi);

    // Tutup statement
    mysqli_stmt_close($stmtSiswa);
    mysqli_stmt_close($stmtCekPenempatan);
    mysqli_stmt_close($stmtInsert);

    $spreadsheet->disconnectWorksheets();

    // ==========================================
    // 10. SIMPAN HASIL IMPOR
    // ==========================================

    $_SESSION['pkl_import_result'] = [
        'berhasil' => $berhasil,
        'dilewati' => $dilewati,
        'gagal' => $gagal,
        'detail' => $detailGagal
    ];

    header('Location: ' . $halamanKembali . '?import=success');
    exit;

} catch (Throwable $e) {

    if (isset($koneksi) && $koneksi instanceof mysqli) {
        try {
            mysqli_rollback($koneksi);
        } catch (Throwable $rollbackError) {
            error_log($rollbackError->getMessage());
        }
    }

    error_log('PKL Import Error: ' . $e->getMessage());

    $_SESSION['pkl_import_result'] = [
        'berhasil' => 0,
        'dilewati' => 0,
        'gagal' => 1,
        'detail' => [
            'Impor gagal. Periksa format file, koneksi database, dan log PHP.'
        ]
    ];

    header('Location: ' . $halamanKembali . '?import=error');
    exit;
}
