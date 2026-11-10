<?php
declare(strict_types=1);

/**
 * Import perusahaan secara massal.
 * Perusahaan lama: hanya status_mou yang diperbarui.
 * Perusahaan baru: seluruh data wajib diisi dan ditambahkan.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Redirect kembali ke halaman perusahaan.
 */
function redirectImport(string $type, string $message): never
{
    header(
        'Location: ../perusahaan.php?' . http_build_query([
            'type' => $type,
            'msg' => $message,
        ])
    );
    exit;
}

/**
 * Normalisasi teks.
 */
function normalisasiImport(string $text): string
{
    $text = trim($text);
    $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

    return function_exists('mb_strtolower')
        ? mb_strtolower($text, 'UTF-8')
        : strtolower($text);
}

/**
 * Normalisasi header Excel.
 */
function normalisasiHeaderImport(string $text): string
{
    $text = preg_replace('/^\xEF\xBB\xBF/', '', trim($text)) ?? trim($text);

    return strtolower(preg_replace('/[^a-z0-9]/i', '', $text) ?? '');
}

/**
 * Validasi status MOU.
 */
function validasiStatusImport(string $status): ?string
{
    $status = normalisasiImport($status);

    $daftarStatus = [
        'proses' => 'Proses',
        'aktif' => 'Aktif',
        'kadaluarsa' => 'Kadaluarsa',
    ];

    return $daftarStatus[$status] ?? null;
}

/**
 * Mengambil nilai berdasarkan kolom Excel.
 */
function ambilNilaiImport(array $row, array $kolom, string $key): string
{
    if (!isset($kolom[$key])) {
        return '';
    }

    $value = $row[$kolom[$key]] ?? '';

    if ($value === null) {
        return '';
    }

    return trim((string) $value);
}

// ========================================
// VALIDASI REQUEST
// ========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectImport('danger', 'Metode request tidak valid.');
}

if (!function_exists('verifyCsrfToken')) {
    redirectImport('danger', 'Fungsi keamanan tidak tersedia.');
}

try {
    // Fungsi ini bertipe void dan melempar exception jika token salah.
    verifyCsrfToken($_POST['csrf_token'] ?? null);
} catch (Throwable $e) {
    redirectImport('danger', 'Token keamanan tidak valid atau sesi telah berakhir.');
}

if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    redirectImport('danger', 'Koneksi database bermasalah.');
}

if (!isset($_FILES['file_import'])) {
    redirectImport('danger', 'Silakan pilih file Excel atau CSV.');
}

$file = $_FILES['file_import'];

if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    redirectImport('danger', 'File gagal diunggah. Silakan coba kembali.');
}

if (!isset($file['size']) || $file['size'] <= 0) {
    redirectImport('danger', 'File yang diunggah kosong.');
}

if ($file['size'] > 5 * 1024 * 1024) {
    redirectImport('danger', 'Ukuran file maksimal 5 MB.');
}

$extension = strtolower(
    pathinfo((string) $file['name'], PATHINFO_EXTENSION)
);

if (!in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
    redirectImport('danger', 'Format file tidak didukung. Gunakan XLSX, XLS, atau CSV.');
}

if (!is_uploaded_file($file['tmp_name'])) {
    redirectImport('danger', 'File unggahan tidak valid.');
}

$spreadsheet = null;
$transaksiDimulai = false;

// ========================================
// PROSES IMPORT
// ========================================

try {

    // Membuat reader berdasarkan ekstensi file.
    if ($extension === 'xlsx') {

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');

    } elseif ($extension === 'xls') {

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');

    } else {

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');

        // Mendeteksi delimiter CSV.
        $handle = fopen($file['tmp_name'], 'r');

        if ($handle === false) {
            throw new RuntimeException('File CSV tidak dapat dibaca.');
        }

        $firstLine = fgets($handle);
        fclose($handle);

        $firstLine = $firstLine === false ? '' : $firstLine;

        $delimiter = substr_count($firstLine, ';') >
            substr_count($firstLine, ',')
            ? ';'
            : ',';

        $reader->setDelimiter($delimiter);
        $reader->setEnclosure('"');
        $reader->setInputEncoding('UTF-8');
    }

    $reader->setReadDataOnly(true);

    $spreadsheet = $reader->load($file['tmp_name']);

    $sheet = $spreadsheet->getActiveSheet();

    $rows = $sheet->toArray(
        null,
        true,
        true,
        false
    );

    if (count($rows) < 2) {
        redirectImport('warning', 'File belum memiliki baris data perusahaan.');
    }

    // ========================================
    // MEMBACA HEADER EXCEL
    // ========================================

    $headerRow = array_shift($rows);

    $aliases = [
        'namaperusahaan' => 'nama_perusahaan',
        'nama' => 'nama_perusahaan',

        'sektorbidang' => 'sektor_bidang',
        'sektor' => 'sektor_bidang',

        'jurusan' => 'jurusan',

        'alamat' => 'alamat',

        'penanggungjawab' => 'penanggung_jawab',
        'pic' => 'penanggung_jawab',

        'notelepon' => 'no_telepon',
        'nomortelepon' => 'no_telepon',
        'nohp' => 'no_telepon',
        'telepon' => 'no_telepon',

        'statusmou' => 'status_mou',
        'status' => 'status_mou',
    ];

    $kolom = [];

    foreach ($headerRow as $index => $header) {

        $key = normalisasiHeaderImport((string) ($header ?? ''));

        if (isset($aliases[$key])) {
            $kolom[$aliases[$key]] = $index;
        }
    }

    // Kolom utama wajib tersedia.
    if (
        !isset($kolom['nama_perusahaan']) ||
        !isset($kolom['status_mou'])
    ) {
        redirectImport(
            'danger',
            'Kolom Nama Perusahaan dan Status MOU wajib tersedia pada baris pertama.'
        );
    }

    // ========================================
    // MENYIAPKAN QUERY DATABASE
    // ========================================

    $cek = $koneksi->prepare("
        SELECT id
        FROM perusahaan
        WHERE LOWER(TRIM(nama_perusahaan)) = LOWER(TRIM(?))
        LIMIT 2
    ");

    $cek->bind_param('s', $nama);

    $update = $koneksi->prepare("
        UPDATE perusahaan
        SET status_mou = ?
        WHERE id = ?
    ");

    $update->bind_param(
        'si',
        $statusMou,
        $idPerusahaanUpdate
    );

    $insert = $koneksi->prepare("
        INSERT INTO perusahaan (
            nama_perusahaan,
            sektor_bidang,
            jurusan,
            alamat,
            penanggung_jawab,
            no_telepon,
            status_mou
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $insert->bind_param(
        'sssssss',
        $nama,
        $sektor,
        $jurusan,
        $alamat,
        $penanggungJawab,
        $telepon,
        $statusMou
    );

    // ========================================
    // INISIALISASI HASIL IMPORT
    // ========================================

    $berhasilTambah = 0;
    $berhasilUpdate = 0;
    $gagal = 0;

    $daftarNama = [];
    $detailGagal = [];

    $koneksi->begin_transaction();
    $transaksiDimulai = true;

    // ========================================
    // PROSES SETIAP BARIS EXCEL
    // ========================================

    foreach ($rows as $index => $row) {

        $nomorBaris = $index + 2;

        $nama = ambilNilaiImport(
            $row,
            $kolom,
            'nama_perusahaan'
        );

        $statusAsli = ambilNilaiImport(
            $row,
            $kolom,
            'status_mou'
        );

        $statusMou = validasiStatusImport($statusAsli);

        // Abaikan baris kosong.
        if ($nama === '' && $statusAsli === '') {
            continue;
        }

        if ($nama === '') {
            $gagal++;

            $detailGagal[] =
                "Baris {$nomorBaris}: Nama perusahaan kosong.";

            continue;
        }

        if ($statusMou === null) {
            $gagal++;

            $detailGagal[] =
                "Baris {$nomorBaris}: Status MOU harus Proses, Aktif, atau Kadaluarsa.";

            continue;
        }

        // Hindari nama perusahaan berulang dalam file.
        $keyNama = normalisasiImport($nama);

        if (isset($daftarNama[$keyNama])) {
            $gagal++;

            $detailGagal[] =
                "Baris {$nomorBaris}: Nama perusahaan duplikat dalam file.";

            continue;
        }

        $daftarNama[$keyNama] = true;

        // ========================================
        // CEK PERUSAHAAN DI DATABASE
        // ========================================

        $cek->execute();
        $cek->store_result();

        $jumlahCocok = $cek->num_rows;

        $idPerusahaan = null;

        if ($jumlahCocok === 1) {
            $cek->bind_result($idPerusahaan);
            $cek->fetch();
        }

        $cek->free_result();

        // Jika data perusahaan ganda di database.
        if ($jumlahCocok > 1) {
            $gagal++;

            $detailGagal[] =
                "Baris {$nomorBaris}: Nama perusahaan memiliki data ganda di database.";

            continue;
        }

        // ========================================
        // PERUSAHAAN SUDAH ADA
        // HANYA UPDATE STATUS MOU
        // ========================================

        if ($jumlahCocok === 1) {

            $idPerusahaanUpdate = (int) $idPerusahaan;

            $update->execute();

            $berhasilUpdate++;

            continue;
        }

        // ========================================
        // PERUSAHAAN BARU
        // AMBIL SELURUH DATA DARI EXCEL
        // ========================================

        $sektor = ambilNilaiImport($row, $kolom, 'sektor_bidang');

        $jurusan = ambilNilaiImport($row, $kolom, 'jurusan');

        $alamat = ambilNilaiImport($row, $kolom, 'alamat');

        $penanggungJawab = ambilNilaiImport(
            $row,
            $kolom,
            'penanggung_jawab'
        );

        $telepon = ambilNilaiImport(
            $row,
            $kolom,
            'no_telepon'
        );

        // Validasi data wajib.
        $dataWajib = [
            'Sektor Bidang' => $sektor,
            'Jurusan' => $jurusan,
            'Alamat' => $alamat,
            'Penanggung Jawab' => $penanggungJawab,
            'No. Telepon' => $telepon,
        ];

        $kolomKosong = [];

        foreach ($dataWajib as $label => $value) {

            if ($value === '') {
                $kolomKosong[] = $label;
            }
        }

        if (!empty($kolomKosong)) {

            $gagal++;

            $detailGagal[] =
                "Baris {$nomorBaris}: Data perusahaan baru belum lengkap (" .
                implode(', ', $kolomKosong) . ").";

            continue;
        }

        // ========================================
        // INSERT PERUSAHAAN BARU
        // ========================================

        $insert->execute();

        $berhasilTambah++;
    }

    // ========================================
    // SIMPAN TRANSAKSI
    // ========================================

    $koneksi->commit();
    $transaksiDimulai = false;

    $cek->close();
    $update->close();
    $insert->close();

    if ($spreadsheet !== null) {
        $spreadsheet->disconnectWorksheets();
    }

    // ========================================
    // LAPORAN HASIL IMPORT
    // ========================================

    $totalBerhasil = $berhasilTambah + $berhasilUpdate;

    $pesan = "Import selesai! " .
        "Ditambahkan: {$berhasilTambah}, " .
        "Diperbarui: {$berhasilUpdate}, " .
        "Gagal: {$gagal}.";

    if (!empty($detailGagal)) {

        $pesan .= ' Detail: ' .
            implode(' | ', array_slice($detailGagal, 0, 3));
    }

    $tipe = $totalBerhasil > 0 ? 'success' : 'warning';

    redirectImport($tipe, $pesan);

} catch (Throwable $e) {

    if (
        $transaksiDimulai &&
        isset($koneksi) &&
        $koneksi instanceof mysqli
    ) {
        try {
            $koneksi->rollback();
        } catch (Throwable $rollbackError) {
            error_log(
                'Rollback import perusahaan: ' .
                $rollbackError->getMessage()
            );
        }
    }

    if ($spreadsheet !== null) {
        try {
            $spreadsheet->disconnectWorksheets();
        } catch (Throwable $ignored) {
            // Abaikan kesalahan saat pembersihan.
        }
    }

    error_log('Import perusahaan: ' . $e->getMessage());

    redirectImport(
        'danger',
        'Import gagal karena terjadi kesalahan. Periksa format file dan log PHP.'
    );
}