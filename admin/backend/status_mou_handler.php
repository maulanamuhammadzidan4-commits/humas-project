<?php
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

const MOU_MAX_FILE_SIZE = 5242880;
const MOU_ALLOWED_FILE_TYPES = [
    'application/pdf' => ['pdf'],
    'image/jpeg' => ['jpg', 'jpeg'],
    'image/png' => ['png'],
    'image/webp' => ['webp'],
];

function redirectStatusMou(string $message, string $type = 'success'): never
{
    redirectWithMessage('../status_mou.php', $message, $type);
}

function validateMouUpload(array $file): array
{
    $uploadError = $file['error'] ?? null;
    if (!is_int($uploadError)) {
        throw new InvalidArgumentException('Data unggahan dokumen tidak valid.');
    }

    if ($uploadError !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Ukuran dokumen melebihi batas konfigurasi server.',
            UPLOAD_ERR_FORM_SIZE => 'Ukuran dokumen melebihi batas formulir.',
            UPLOAD_ERR_PARTIAL => 'Dokumen hanya terunggah sebagian. Silakan coba kembali.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server tidak memiliki direktori sementara untuk unggahan.',
            UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis dokumen yang diunggah.',
            UPLOAD_ERR_EXTENSION => 'Unggahan dihentikan oleh konfigurasi server.',
        ];
        throw new InvalidArgumentException(
            $messages[$uploadError] ?? 'Unggahan dokumen gagal dengan kode ' . $uploadError . '.'
        );
    }

    $temporaryPath = $file['tmp_name'] ?? null;
    $originalName = $file['name'] ?? null;
    $reportedSize = $file['size'] ?? null;
    if (
        !is_string($temporaryPath)
        || !is_uploaded_file($temporaryPath)
        || !is_string($originalName)
        || !is_int($reportedSize)
    ) {
        throw new InvalidArgumentException('Data dokumen yang diunggah tidak valid.');
    }

    $actualSize = filesize($temporaryPath);
    if ($actualSize === false || $actualSize < 1) {
        throw new InvalidArgumentException('Dokumen kosong atau tidak dapat dibaca.');
    }
    if ($actualSize > MOU_MAX_FILE_SIZE || $reportedSize > MOU_MAX_FILE_SIZE) {
        throw new InvalidArgumentException('Ukuran dokumen maksimal 5 MB.');
    }
    if ($reportedSize !== $actualSize) {
        throw new InvalidArgumentException('Ukuran dokumen tidak valid.');
    }

    $safeOriginalName = basename(str_replace('\\', '/', $originalName));
    $safeOriginalName = preg_replace('/[\x00-\x1F\x7F]/u', '', $safeOriginalName);
    if (!is_string($safeOriginalName) || trim($safeOriginalName) === '') {
        throw new InvalidArgumentException('Nama dokumen tidak valid.');
    }
    $safeOriginalName = validateString($safeOriginalName, 'Nama dokumen', 1, 255);

    $extension = strtolower(pathinfo($safeOriginalName, PATHINFO_EXTENSION));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($temporaryPath);
    if (
        !is_string($mimeType)
        || !isset(MOU_ALLOWED_FILE_TYPES[$mimeType])
        || !in_array($extension, MOU_ALLOWED_FILE_TYPES[$mimeType], true)
    ) {
        throw new InvalidArgumentException('Tipe dokumen tidak sesuai. Gunakan PDF, JPEG, PNG, atau WebP dengan ekstensi yang cocok.');
    }

    return [
        'temporary_path' => $temporaryPath,
        'original_filename' => $safeOriginalName,
        'mime_type' => $mimeType,
        'extension' => $extension === 'jpeg' ? 'jpg' : $extension,
        'file_size' => $actualSize,
    ];
}

function sendMouDocument(mysqli $connection): never
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('Allow: GET');
        http_response_code(405);
        exit('Metode permintaan tidak diizinkan.');
    }

    $perusahaanId = validateInteger($_GET['perusahaan_id'] ?? null, 'ID perusahaan', 1, 2147483647);
    $record = PerusahaanMouRepository::getByPerusahaanId($connection, $perusahaanId);
    if ($record === null || !is_string($record['file_path'] ?? null) || $record['file_path'] === '') {
        http_response_code(404);
        exit('Dokumen MoU tidak ditemukan.');
    }

    $storageDirectory = perusahaanMouStorageDirectory();
    $filePath = $storageDirectory === null
        ? null
        : perusahaanMouStoredFilePath($record['file_path'], $storageDirectory);
    $mimeType = $record['mime_type'] ?? null;
    $recordedSize = $record['file_size'] ?? null;
    if (
        $filePath === null
        || !is_string($mimeType)
        || !isset(MOU_ALLOWED_FILE_TYPES[$mimeType])
        || !is_numeric($recordedSize)
        || !is_file($filePath)
        || !is_readable($filePath)
    ) {
        http_response_code(404);
        exit('Dokumen MoU tidak tersedia.');
    }

    $actualSize = filesize($filePath);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $actualMimeType = $finfo->file($filePath);
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if (
        $actualSize === false
        || $actualSize !== (int)$recordedSize
        || $actualMimeType !== $mimeType
        || !in_array($extension, MOU_ALLOWED_FILE_TYPES[$mimeType], true)
    ) {
        error_log('Validasi MIME atau ukuran dokumen MoU gagal untuk perusahaan ID ' . $perusahaanId . '.');
        http_response_code(404);
        exit('Dokumen MoU tidak tersedia.');
    }

    $stream = fopen($filePath, 'rb');
    if ($stream === false) {
        throw new RuntimeException('Dokumen MoU gagal dibuka.');
    }

    $originalName = is_string($record['original_filename'] ?? null)
        ? basename(str_replace('\\', '/', str_replace(["\r", "\n", "\0"], '', $record['original_filename'])))
        : 'dokumen-mou.' . $extension;
    if ($originalName === '') {
        $originalName = 'dokumen-mou.' . $extension;
    }
    $fallbackName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
    if (!is_string($fallbackName) || $fallbackName === '') {
        $fallbackName = 'dokumen-mou.' . $extension;
    }

    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . $actualSize);
    header('Content-Disposition: attachment; filename="' . addcslashes($fallbackName, "\\\"") . '"; filename*=UTF-8\'\'' . rawurlencode($originalName));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store, max-age=0');
    fpassthru($stream);
    fclose($stream);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'] ?? '';
    if ($method === 'GET') {
        if (($_GET['action'] ?? '') !== 'download') {
            throw new InvalidArgumentException('Aksi Status MoU tidak valid.');
        }
        sendMouDocument($koneksi);
    }

    if ($method !== 'POST') {
        header('Allow: GET, POST');
        http_response_code(405);
        exit('Metode permintaan tidak diizinkan.');
    }

    if (
        empty($_POST)
        && isset($_SERVER['CONTENT_LENGTH'])
        && is_string($_SERVER['CONTENT_LENGTH'])
        && ctype_digit($_SERVER['CONTENT_LENGTH'])
    ) {
        $postMaxSize = ini_get('post_max_size');
        if (is_string($postMaxSize) && preg_match('/^\s*(\d+)\s*([KMG])?B?\s*$/i', $postMaxSize, $matches)) {
            $unit = strtoupper($matches[2] ?? '');
            $multiplier = match ($unit) {
                'G' => 1024 ** 3,
                'M' => 1024 ** 2,
                'K' => 1024,
                default => 1,
            };
            $postMaxBytes = (int)$matches[1] * $multiplier;
            if ($postMaxBytes > 0 && (int)$_SERVER['CONTENT_LENGTH'] > $postMaxBytes) {
                throw new InvalidArgumentException('Total unggahan melebihi batas konfigurasi server.');
            }
        }
    }

    if (($_POST['action'] ?? '') !== 'save') {
        throw new InvalidArgumentException('Aksi Status MoU tidak valid.');
    }

    verifyCsrfToken($_POST['csrf_token'] ?? null);
    $perusahaanId = validateInteger($_POST['perusahaan_id'] ?? null, 'ID perusahaan', 1, 2147483647);
    $status = validateEnum($_POST['status'] ?? null, ['Proses', 'Aktif', 'Kadaluarsa'], 'status MoU');

    $currentRecord = PerusahaanMouRepository::getByPerusahaanId($koneksi, $perusahaanId);
    if ($currentRecord === null) {
        throw new InvalidArgumentException('Perusahaan yang dipilih tidak ditemukan.');
    }

    $upload = null;
    if (array_key_exists('mou_file', $_FILES) && !is_array($_FILES['mou_file'])) {
        throw new InvalidArgumentException('Data unggahan dokumen MoU tidak valid.');
    }
    if (
        isset($_FILES['mou_file'])
        && ($_FILES['mou_file']['error'] ?? null) !== UPLOAD_ERR_NO_FILE
    ) {
        $upload = validateMouUpload($_FILES['mou_file']);
    }

    if ($upload === null) {
        if (!PerusahaanMouRepository::saveStatus($koneksi, $perusahaanId, $status)) {
            throw new RuntimeException('Gagal menyimpan status MoU.');
        }
        redirectStatusMou('Status MoU berhasil diperbarui.');
    }

    $storageDirectory = perusahaanMouStorageDirectory(true);
    $newFileName = bin2hex(random_bytes(32)) . '.' . $upload['extension'];
    $newFilePath = $storageDirectory . DIRECTORY_SEPARATOR . $newFileName;
    if (!move_uploaded_file($upload['temporary_path'], $newFilePath)) {
        if (is_file($newFilePath) && !unlink($newFilePath)) {
            error_log('Gagal membersihkan berkas MoU setelah pemindahan gagal: ' . $newFilePath);
        }
        throw new RuntimeException('Dokumen gagal disimpan. Status dan dokumen lama tidak diubah.');
    }
    if (!chmod($newFilePath, 0600)) {
        if (!unlink($newFilePath)) {
            error_log('Gagal membersihkan berkas MoU baru setelah chmod gagal: ' . $newFilePath);
        }
        throw new RuntimeException('Izin akses dokumen gagal diamankan. Status dan dokumen lama tidak diubah.');
    }

    try {
        if (!PerusahaanMouRepository::saveStatusAndFile(
            $koneksi,
            $perusahaanId,
            $status,
            $newFileName,
            $upload['original_filename'],
            $upload['mime_type'],
            $upload['file_size']
        )) {
            throw new RuntimeException('Gagal menyimpan status dan metadata dokumen MoU.');
        }
    } catch (Throwable $exception) {
        if (!unlink($newFilePath)) {
            error_log('Gagal membersihkan berkas MoU baru setelah penyimpanan database gagal: ' . $newFilePath);
        }
        throw $exception;
    }

    $oldFileName = $currentRecord['file_path'] ?? null;
    $oldFileCleanupFailed = false;
    if (is_string($oldFileName) && $oldFileName !== '') {
        $oldFilePath = perusahaanMouStoredFilePath($oldFileName, $storageDirectory);
        if ($oldFilePath === null) {
            $oldFileCleanupFailed = true;
            error_log('Path berkas MoU lama tidak valid setelah penggantian untuk perusahaan ID ' . $perusahaanId . '.');
        } elseif (is_file($oldFilePath) && !unlink($oldFilePath)) {
            $oldFileCleanupFailed = true;
            error_log('Gagal menghapus berkas MoU lama setelah penggantian untuk perusahaan ID ' . $perusahaanId . '.');
        }
    }

    if ($oldFileCleanupFailed) {
        redirectStatusMou('Status dan dokumen baru tersimpan, tetapi dokumen lama gagal dihapus. Hubungi administrator.', 'warning');
    }

    redirectStatusMou('Status dan dokumen MoU berhasil diperbarui.');
} catch (InvalidArgumentException $exception) {
    redirectStatusMou('Gagal: ' . $exception->getMessage(), 'danger');
} catch (mysqli_sql_exception $exception) {
    error_log('Kesalahan database pada handler Status MoU: ' . $exception->getMessage());
    redirectStatusMou('Gagal menyimpan Status MoU karena terjadi kesalahan database.', 'danger');
} catch (RuntimeException $exception) {
    redirectStatusMou('Gagal: ' . $exception->getMessage(), 'danger');
} catch (Throwable $exception) {
    error_log('Kesalahan handler Status MoU: ' . $exception->getMessage());
    redirectStatusMou('Gagal memproses Status MoU karena terjadi kesalahan server.', 'danger');
}
