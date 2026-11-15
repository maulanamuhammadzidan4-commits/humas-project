
<?php
/**
 * Handler Import — Penempatan PKL
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../backend/helpers.php';
require_once __DIR__ . '/../../backend/repositories/bootstrap.php';

$authLoginPath = '../login_admin.php';
require_once __DIR__ . '/../includes/auth.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/*
|--------------------------------------------------------------------------
| KONFIGURASI
|--------------------------------------------------------------------------
*/

$berhasil = 0;
$dilewati = 0;
$gagal = 0;
$detailGagal = [];

$maksimalUkuran = 5 * 1024 * 1024;

$allowedStatus = [
    'Draft',
    'Disetujui',
    'Selesai'
];

/*
|--------------------------------------------------------------------------
| FUNGSI BANTU
|--------------------------------------------------------------------------
*/

function pklNormalizeHeader(string $header): string
{
    return strtolower(
        preg_replace('/[^a-zA-Z0-9]/', '', trim($header))
    );
}


function pklGetCellValue($sheet, int $column, int $row): string
{
    return trim((string) $sheet
        ->getCell([$column, $row])
        ->getFormattedValue());
}


function pklConvertDate($value): string
{
    if ($value === null || $value === '') {
        throw new RuntimeException('Tanggal belum diisi.');
    }

    // Tanggal Excel berbentuk angka serial.
    if (is_numeric($value)) {
        return ExcelDate::excelToDateTimeObject((float) $value)
            ->format('Y-m-d');
    }

    $value = trim((string) $value);

    $formats = [
        'Y-m-d',
        'd/m/Y',
        'd-m-Y',
        'm/d/Y',
        'd.m.Y'
    ];

    foreach ($formats as $format) {
        $date = DateTime::createFromFormat('!' . $format, $value);

        if (
            $date !== false &&
            $date->format($format) === $value
        ) {
            return $date->format('Y-m-d');
        }
    }

    throw new RuntimeException(
        "Format tanggal '{$value}' tidak valid."
    );
}


function pklFindSiswaId(
    mysqli $koneksi,
    string $nisn
): ?int {

    $stmt = $koneksi->prepare(
        "SELECT id
         FROM siswa
         WHERE TRIM(nisn) = ?
         LIMIT 2"
    );

    $stmt->bind_param('s', $nisn);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows !== 1) {
        $stmt->close();
        return null;
    }

    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    return (int) $id;
}


function pklFindPerusahaanId(
    mysqli $koneksi,
    string $nama
): ?int {

    $stmt = $koneksi->prepare(
        "SELECT id
         FROM perusahaan
         WHERE LOWER(TRIM(nama_perusahaan)) = LOWER(?)
         LIMIT 2"
    );

    $stmt->bind_param('s', $nama);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows !== 1) {
        $stmt->close();
        return null;
    }

    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    return (int) $id;
}


function pklIsDuplicate(
    mysqli $koneksi,
    int $siswaId,
    int $perusahaanId,
    string $tanggalMulai,
    string $tanggalSelesai
): bool {

    $stmt = $koneksi->prepare(
        "SELECT id
         FROM pkl_penempatan
         WHERE siswa_id = ?
           AND perusahaan_id = ?
           AND tanggal_mulai = ?
           AND tanggal_selesai = ?
         LIMIT 1"
    );

    $stmt->bind_param(
        'iiss',
        $siswaId,
        $perusahaanId,
        $tanggalMulai,
        $tanggalSelesai
    );

    $stmt->execute();
    $stmt->store_result();

    $duplicate = $stmt->num_rows > 0;

    $stmt->close();

    return $duplicate;
}


/*
|--------------------------------------------------------------------------
| PROSES IMPOR
|--------------------------------------------------------------------------
*/

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException(
            'Metode pengiriman tidak valid.'
        );
    }

    if (!$koneksi) {
        throw new RuntimeException(
            'Koneksi database gagal.'
        );
    }

    // Verifikasi CSRF sesuai form di pkl.php.
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    if (!isset($_FILES['file_import'])) {
        throw new RuntimeException(
            'File Excel belum dipilih.'
        );
    }

    $file = $_FILES['file_import'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException(
            'Upload file gagal. Kode: ' . $file['error']
        );
    }

    if ($file['size'] > $maksimalUkuran) {
        throw new RuntimeException(
            'Ukuran file maksimal 5 MB.'
        );
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException(
            'File upload tidak valid.'
        );
    }

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    if (!in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
        throw new RuntimeException(
            'Format file harus XLSX, XLS, atau CSV.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBACA FILE
    |--------------------------------------------------------------------------
    */

    $reader = IOFactory::createReaderForFile(
        $file['tmp_name']
    );

    $reader->setReadDataOnly(true);

    $spreadsheet = $reader->load($file['tmp_name']);

    $sheet = $spreadsheet->getActiveSheet();

    $lastRow = $sheet->getHighestDataRow();

    $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate
        ::columnIndexFromString(
            $sheet->getHighestDataColumn()
        );

    /*
    |--------------------------------------------------------------------------
    | MEMBACA HEADER EXCEL
    |--------------------------------------------------------------------------
    */

    $headers = [];

    for ($column = 1; $column <= $lastColumn; $column++) {

        $header = pklNormalizeHeader(
            pklGetCellValue($sheet, $column, 1)
        );

        if ($header !== '') {
            $headers[$header] = $column;
        }
    }

    $columnNisn = $headers['nisn'] ?? null;
    $columnNama = $headers['namasiswa'] ?? null;
    $columnIduka = $headers['iduka'] ?? null;

    $columnGuru =
        $headers['gurupembingbing']
        ?? $headers['gurupembimbing']
        ?? null;

    $columnMulai = $headers['tanggalmulai'] ?? null;
    $columnSelesai = $headers['tanggalselesai'] ?? null;

    $columnStatus = $headers['statuspenempatan'] ?? null;

    if (
        !$columnNisn ||
        !$columnNama ||
        !$columnIduka ||
        !$columnGuru ||
        !$columnMulai ||
        !$columnSelesai
    ) {
        throw new RuntimeException(
            'Header Excel tidak lengkap. Pastikan terdapat kolom: nisn, nama siswa, iduka, guru pembingbing, tanggal_mulai, dan tanggal_selesai.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES SETIAP BARIS
    |--------------------------------------------------------------------------
    */

    for ($row = 2; $row <= $lastRow; $row++) {

        $nisn = pklGetCellValue(
            $sheet,
            $columnNisn,
            $row
        );

        $namaSiswa = pklGetCellValue(
            $sheet,
            $columnNama,
            $row
        );

        $namaPerusahaan = pklGetCellValue(
            $sheet,
            $columnIduka,
            $row
        );

        $guru = pklGetCellValue(
            $sheet,
            $columnGuru,
            $row
        );

        $rawMulai = $sheet
            ->getCell([$columnMulai, $row])
            ->getValue();

        $rawSelesai = $sheet
            ->getCell([$columnSelesai, $row])
            ->getValue();

        $status = $columnStatus
            ? pklGetCellValue($sheet, $columnStatus, $row)
            : 'Draft';

        // Lewati baris yang benar-benar kosong.
        if (
            $nisn === '' &&
            $namaSiswa === '' &&
            $namaPerusahaan === '' &&
            $guru === ''
        ) {
            $dilewati++;
            continue;
        }

        $transactionStarted = false;

        try {

            if (
                $nisn === '' ||
                $namaSiswa === '' ||
                $namaPerusahaan === '' ||
                $guru === ''
            ) {
                throw new RuntimeException(
                    'NISN, nama siswa, IDUKA, atau guru pembimbing kosong.'
                );
            }

            if (strlen($guru) > 100) {
                throw new RuntimeException(
                    'Nama guru pembimbing melebihi 100 karakter.'
                );
            }

            $tanggalMulai = pklConvertDate($rawMulai);
            $tanggalSelesai = pklConvertDate($rawSelesai);

            if ($tanggalSelesai < $tanggalMulai) {
                throw new RuntimeException(
                    'Tanggal selesai lebih awal daripada tanggal mulai.'
                );
            }

            if ($status === '') {
                $status = 'Draft';
            }

            if (!in_array($status, $allowedStatus, true)) {
                throw new RuntimeException(
                    "Status '{$status}' tidak dikenal."
                );
            }

            /*
             * Cari siswa berdasarkan NISN.
             */

            $siswaId = pklFindSiswaId(
                $koneksi,
                $nisn
            );

            if ($siswaId === null) {
                throw new RuntimeException(
                    "Siswa dengan NISN {$nisn} tidak ditemukan atau tidak unik."
                );
            }

            /*
             * Cari perusahaan berdasarkan nama IDUKA.
             */

            $perusahaanId = pklFindPerusahaanId(
                $koneksi,
                $namaPerusahaan
            );

            if ($perusahaanId === null) {
                throw new RuntimeException(
                    "Perusahaan '{$namaPerusahaan}' tidak ditemukan atau tidak unik."
                );
            }

            /*
             * Periksa duplikasi.
             */

            if (
                pklIsDuplicate(
                    $koneksi,
                    $siswaId,
                    $perusahaanId,
                    $tanggalMulai,
                    $tanggalSelesai
                )
            ) {
                $dilewati++;
                continue;
            }

            /*
             * Simpan data.
             */

            $koneksi->begin_transaction();
            $transactionStarted = true;

            $dataPkl = [
                'siswa_id' => $siswaId,
                'perusahaan_id' => $perusahaanId,
                'pembimbing_guru' => $guru,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status_penempatan' => $status
            ];

            if (!PklRepository::create($koneksi, $dataPkl)) {
                throw new RuntimeException(
                    'Gagal menyimpan data penempatan PKL.'
                );
            }

            $koneksi->commit();
            $transactionStarted = false;

            $berhasil++;

        } catch (Throwable $e) {

            if ($transactionStarted) {
                try {
                    $koneksi->rollback();
                } catch (Throwable $rollbackError) {
                    error_log(
                        'PKL rollback error: ' .
                        $rollbackError->getMessage()
                    );
                }
            }

            $gagal++;

            $detailGagal[] =
                "Baris {$row}: " . $e->getMessage();

            error_log(
                "PKL IMPORT BARIS {$row}: " .
                $e->getMessage()
            );
        }
    }
    
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);

    $detail = [];

    if (!empty($detailGagal)) {
        $detail = array_slice($detailGagal, 0, 20);

        if (count($detailGagal) > 20) {
            $detail[] =
                'Masih ada ' .
                (count($detailGagal) - 20) .
                ' kesalahan lainnya.';
        }
    }

    $_SESSION['pkl_import_result'] = [
        'berhasil' => $berhasil,
        'dilewati' => $dilewati,
        'gagal' => $gagal,
        'detail' => $detail
    ];

    header('Location: ../pkl.php?import=success');
    exit;

} catch (Throwable $e) {

    error_log(
        'PKL IMPORT ERROR: ' .
        $e->getMessage() .
        ' | File: ' . $e->getFile() .
        ' | Line: ' . $e->getLine()
    );

    $_SESSION['pkl_import_result'] = [
        'berhasil' => $berhasil,
        'dilewati' => $dilewati,
        'gagal' => $gagal + 1,
        'detail' => [$e->getMessage()]
    ];

    header('Location: ../pkl.php?import=error');
    exit;
}
