
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


function pklFetchIdsByValues(
    mysqli $connection,
    string $table,
    string $column,
    array $values,
    bool $caseInsensitive = false
): array {
    if (
        !in_array([$table, $column], [['siswa', 'nisn'], ['perusahaan', 'nama_perusahaan']], true)
    ) {
        throw new InvalidArgumentException('Kolom pencarian impor tidak valid.');
    }

    $matches = [];

    foreach (array_chunk(array_values(array_unique($values)), 200) as $chunk) {
        $sourceRows = implode(
            ' UNION ALL ',
            array_fill(0, count($chunk), 'SELECT ? AS lookup_value')
        );
        $comparison = $caseInsensitive
            ? "LOWER(TRIM(target.`{$column}`)) = LOWER(source.lookup_value)"
            : "TRIM(target.`{$column}`) = source.lookup_value";

        $stmt = $connection->prepare(
            "SELECT source.lookup_value, target.id
             FROM ({$sourceRows}) AS source
             LEFT JOIN `{$table}` AS target ON {$comparison}"
        );

        $types = str_repeat('s', count($chunk));
        $parameters = [$types];
        foreach ($chunk as &$value) {
            $parameters[] = &$value;
        }
        unset($value);
        call_user_func_array([$stmt, 'bind_param'], $parameters);

        $stmt->execute();
        $stmt->bind_result($lookupValue, $id);

        while ($stmt->fetch()) {
            if ($id !== null) {
                $matches[$lookupValue][] = (int) $id;
            }
        }

        $stmt->close();
    }

    return $matches;
}

function pklDuplicateKey(
    int $siswaId,
    int $perusahaanId,
    string $tanggalMulai,
    string $tanggalSelesai
): string {
    return implode('|', [
        $siswaId,
        $perusahaanId,
        $tanggalMulai,
        $tanggalSelesai,
    ]);
}

function pklFetchExistingDuplicates(mysqli $connection, array $records): array
{
    $duplicates = [];

    foreach (array_chunk($records, 100) as $chunk) {
        $sourceRows = [];
        $types = '';
        $values = [];

        foreach ($chunk as $record) {
            $sourceRows[] = 'SELECT ? AS siswa_id, ? AS perusahaan_id, ? AS tanggal_mulai, ? AS tanggal_selesai';
            $types .= 'iiss';
            $values[] = $record['siswa_id'];
            $values[] = $record['perusahaan_id'];
            $values[] = $record['tanggal_mulai'];
            $values[] = $record['tanggal_selesai'];
        }

        $stmt = $connection->prepare(
            'SELECT source.siswa_id, source.perusahaan_id,
                    source.tanggal_mulai, source.tanggal_selesai,
                    placement.id
             FROM (' . implode(' UNION ALL ', $sourceRows) . ') AS source
             LEFT JOIN pkl_penempatan AS placement
               ON placement.siswa_id = source.siswa_id
              AND placement.perusahaan_id = source.perusahaan_id
              AND placement.tanggal_mulai = source.tanggal_mulai
              AND placement.tanggal_selesai = source.tanggal_selesai'
        );

        $parameters = [$types];
        foreach ($values as &$value) {
            $parameters[] = &$value;
        }
        unset($value);
        call_user_func_array([$stmt, 'bind_param'], $parameters);

        $stmt->execute();
        $stmt->bind_result(
            $siswaId,
            $perusahaanId,
            $tanggalMulai,
            $tanggalSelesai,
            $placementId
        );

        while ($stmt->fetch()) {
            if ($placementId !== null) {
                $duplicates[pklDuplicateKey(
                    (int) $siswaId,
                    (int) $perusahaanId,
                    $tanggalMulai,
                    $tanggalSelesai
                )] = true;
            }
        }

        $stmt->close();
    }

    return $duplicates;
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

        $header = normalizeSpreadsheetHeader(
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

    $nisnValues = [];
    $companyValues = [];
    for ($lookupRow = 2; $lookupRow <= $lastRow; $lookupRow++) {
        $nisnValue = pklGetCellValue($sheet, $columnNisn, $lookupRow);
        $companyValue = pklGetCellValue($sheet, $columnIduka, $lookupRow);

        if ($nisnValue !== '') {
            $nisnValues[] = $nisnValue;
        }
        if ($companyValue !== '') {
            $companyValues[] = $companyValue;
        }
    }

    $siswaIdsByNisn = pklFetchIdsByValues(
        $koneksi,
        'siswa',
        'nisn',
        $nisnValues
    );
    $perusahaanIdsByName = pklFetchIdsByValues(
        $koneksi,
        'perusahaan',
        'nama_perusahaan',
        $companyValues,
        true
    );

    $insertPkl = $koneksi->prepare(
        "INSERT INTO pkl_penempatan
            (siswa_id, perusahaan_id, pembimbing_guru, tanggal_mulai, tanggal_selesai, status_penempatan)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $siswaIdForInsert = 0;
    $perusahaanIdForInsert = 0;
    $guruForInsert = '';
    $tanggalMulaiForInsert = '';
    $tanggalSelesaiForInsert = '';
    $statusForInsert = '';
    $insertPkl->bind_param(
        'iissss',
        $siswaIdForInsert,
        $perusahaanIdForInsert,
        $guruForInsert,
        $tanggalMulaiForInsert,
        $tanggalSelesaiForInsert,
        $statusForInsert
    );

    $rowsToInsert = [];

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

            $siswaIds = $siswaIdsByNisn[$nisn] ?? [];
            $siswaId = count($siswaIds) === 1 ? $siswaIds[0] : null;

            if ($siswaId === null) {
                throw new RuntimeException(
                    "Siswa dengan NISN {$nisn} tidak ditemukan atau tidak unik."
                );
            }

            /*
             * Cari perusahaan berdasarkan nama IDUKA.
             */

            $perusahaanIds = $perusahaanIdsByName[$namaPerusahaan] ?? [];
            $perusahaanId = count($perusahaanIds) === 1
                ? $perusahaanIds[0]
                : null;

            if ($perusahaanId === null) {
                throw new RuntimeException(
                    "Perusahaan '{$namaPerusahaan}' tidak ditemukan atau tidak unik."
                );
            }

            $rowsToInsert[] = [
                'row' => $row,
                'siswa_id' => $siswaId,
                'perusahaan_id' => $perusahaanId,
                'pembimbing_guru' => $guru,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status_penempatan' => $status,
            ];

        } catch (Throwable $e) {
            $gagal++;

            $detailGagal[] =
                "Baris {$row}: " . $e->getMessage();

            error_log(
                "PKL IMPORT BARIS {$row}: " .
                $e->getMessage()
            );
        }
    }

    $duplicateCache = pklFetchExistingDuplicates($koneksi, $rowsToInsert);

    foreach ($rowsToInsert as $record) {
        $duplicateKey = pklDuplicateKey(
            $record['siswa_id'],
            $record['perusahaan_id'],
            $record['tanggal_mulai'],
            $record['tanggal_selesai']
        );

        if ($duplicateCache[$duplicateKey] ?? false) {
            $dilewati++;
            continue;
        }

        try {
            $siswaIdForInsert = $record['siswa_id'];
            $perusahaanIdForInsert = $record['perusahaan_id'];
            $guruForInsert = $record['pembimbing_guru'];
            $tanggalMulaiForInsert = $record['tanggal_mulai'];
            $tanggalSelesaiForInsert = $record['tanggal_selesai'];
            $statusForInsert = $record['status_penempatan'];

            if (!$insertPkl->execute()) {
                throw new RuntimeException(
                    'Gagal menyimpan data penempatan PKL.'
                );
            }

            $duplicateCache[$duplicateKey] = true;
            $berhasil++;
        } catch (Throwable $e) {
            $gagal++;
            $detailGagal[] = "Baris {$record['row']}: " . $e->getMessage();

            error_log(
                "PKL IMPORT BARIS {$record['row']}: " .
                $e->getMessage()
            );
        }
    }

    $insertPkl->close();
    
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
