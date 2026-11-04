<?php

function formatTanggalIndo(?string $tanggal): string
{
    if (empty($tanggal)) {
        return '-';
    }

    $timestamp = strtotime($tanggal);
    if ($timestamp === false) {
        return htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8');
    }

    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    return date('d', $timestamp) . ' ' . $bulan[(int) date('m', $timestamp)] . ' ' . date('Y', $timestamp);
}

function render_pipe_list(string $value): string
{
    $items = array_filter(array_map('trim', explode('|', $value)));
    $html = '';

    foreach ($items as $item) {
        $html .= '<li><i class="fa-solid fa-check"></i><span>'
            . htmlspecialchars($item, ENT_QUOTES, 'UTF-8')
            . '</span></li>';
    }

    return $html;
}

function getBeritaById($koneksi, int $id): ?array
{
    if ($id <= 0 || !$koneksi) {
        return null;
    }

    require_once __DIR__ . '/repositories/bootstrap.php';
    return BeritaRepository::findById($koneksi, $id);
}

function redirectWithMessage(string $target, string $message = '', string $type = 'success'): never
{
    $query = http_build_query([
        'msg' => $message,
        'type' => $type,
    ]);

    $separator = str_contains($target, '?') ? '&' : '?';
    $url = $message === '' ? $target : $target . $separator . $query;

    header('Location: ' . $url);
    exit;
}

function requireNonEmptyFields(array $fields, string $errorMessage): void
{
    foreach ($fields as $field) {
        if (trim((string) $field) === '') {
            throw new InvalidArgumentException($errorMessage);
        }
    }
}

function validateString(mixed $value, string $fieldLabel, int $minLength = 1, int $maxLength = 255): string
{
    if (!is_string($value)) {
        throw new InvalidArgumentException("{$fieldLabel} tidak valid.");
    }

    $value = trim($value);
    if (function_exists('mb_strlen')) {
        $length = mb_strlen($value, 'UTF-8');
    } else {
        $length = preg_match_all('/./us', $value);
        if ($length === false) {
            throw new InvalidArgumentException("{$fieldLabel} harus menggunakan teks UTF-8 yang valid.");
        }
    }

    if ($length < $minLength || $length > $maxLength) {
        throw new InvalidArgumentException("{$fieldLabel} harus memiliki panjang antara {$minLength} dan {$maxLength} karakter.");
    }

    return $value;
}

function validateEnum(mixed $value, array $allowedValues, string $fieldLabel): string
{
    if (!is_string($value) || !in_array($value, $allowedValues, true)) {
        throw new InvalidArgumentException("Nilai {$fieldLabel} tidak valid.");
    }

    return $value;
}

function validateDate(mixed $value, string $fieldLabel): string
{
    if (!is_string($value)) {
        throw new InvalidArgumentException("Format {$fieldLabel} tidak valid (gunakan YYYY-MM-DD).");
    }

    $date = DateTime::createFromFormat('!Y-m-d', $value);
    $errors = DateTime::getLastErrors();
    if (
        $date === false
        || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        || $date->format('Y-m-d') !== $value
    ) {
        throw new InvalidArgumentException("Format {$fieldLabel} tidak valid (gunakan YYYY-MM-DD).");
    }

    return $value;
}

function validateInteger(mixed $value, string $fieldLabel, int $min, int $max): int
{
    if (!is_string($value) && !is_int($value)) {
        throw new InvalidArgumentException("{$fieldLabel} harus berupa bilangan bulat.");
    }

    $integer = filter_var($value, FILTER_VALIDATE_INT);
    if ($integer === false || $integer < $min || $integer > $max) {
        throw new InvalidArgumentException("{$fieldLabel} harus berupa bilangan bulat antara {$min} dan {$max}.");
    }

    return $integer;
}

function validatePassword(mixed $value): string
{
    if (!is_string($value) || trim($value) === '') {
        throw new InvalidArgumentException('Password tidak boleh kosong.');
    }

    if (function_exists('mb_strlen')) {
        $length = mb_strlen($value, 'UTF-8');
    } else {
        $length = preg_match_all('/./us', $value);
        if ($length === false) {
            throw new InvalidArgumentException('Password harus menggunakan teks UTF-8 yang valid.');
        }
    }

    if ($length < 6 || strlen($value) > 72) {
        throw new InvalidArgumentException('Password minimal 6 karakter dan maksimal 72 byte.');
    }

    return $value;
}

function generateCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (
        !is_string($_SESSION['csrf_token'] ?? null)
        || !preg_match('/^[a-f0-9]{64}$/D', $_SESSION['csrf_token'])
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(mixed $token): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (
        !is_string($token)
        || !is_string($sessionToken)
        || !preg_match('/^[a-f0-9]{64}$/D', $sessionToken)
        || !preg_match('/^[a-f0-9]{64}$/D', $token)
        || !hash_equals($sessionToken, $token)
    ) {
        throw new RuntimeException('Token keamanan (CSRF) tidak valid atau sesi telah berakhir.');
    }
}

function ensureAdminAuthenticated(string $loginPath = 'login_admin.php'): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . $loginPath);
        exit;
    }

    unset($_SESSION['login_success_flash']);
}

function perusahaanMouStorageDirectory(bool $create = false): ?string
{
    $configuredDirectory = getenv('HUMAS_MOU_STORAGE_DIR');
    $usingDefaultDirectory = $configuredDirectory === false || $configuredDirectory === '';
    $projectRoot = realpath(dirname(__DIR__));
    if ($projectRoot === false) {
        throw new RuntimeException('Direktori proyek tidak dapat ditentukan.');
    }

    $directory = $usingDefaultDirectory
        ? $projectRoot . DIRECTORY_SEPARATOR . 'mou'
        : $configuredDirectory;

    if ($directory[0] !== DIRECTORY_SEPARATOR) {
        throw new RuntimeException('Lokasi penyimpanan dokumen MoU harus berupa path absolut.');
    }

    if (!is_dir($directory)) {
        if (!$create) {
            return null;
        }
        if (!mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new RuntimeException('Direktori penyimpanan dokumen MoU tidak dapat dibuat.');
        }
    }

    $resolvedDirectory = realpath($directory);
    if (
        $resolvedDirectory === false
        || !is_dir($resolvedDirectory)
        || !is_writable($resolvedDirectory)
        || ($usingDefaultDirectory && $resolvedDirectory !== $directory)
        || (!$usingDefaultDirectory && (
            $resolvedDirectory === $projectRoot
            || str_starts_with($resolvedDirectory, $projectRoot . DIRECTORY_SEPARATOR)
        ))
    ) {
        throw new RuntimeException('Direktori penyimpanan dokumen MoU tidak valid.');
    }

    if ($usingDefaultDirectory) {
        if (!chmod($resolvedDirectory, 0700)) {
            throw new RuntimeException('Izin akses direktori dokumen MoU gagal diamankan.');
        }

        $accessControlPath = $resolvedDirectory . DIRECTORY_SEPARATOR . '.htaccess';
        $denyRules = "Options -Indexes\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order allow,deny\n    Deny from all\n</IfModule>\n";
        $accessControlRules = is_file($accessControlPath)
            ? file_get_contents($accessControlPath)
            : false;

        if ($accessControlRules === false && $create) {
            $writtenBytes = file_put_contents($accessControlPath, $denyRules, LOCK_EX);
            if ($writtenBytes !== strlen($denyRules) || !chmod($accessControlPath, 0600)) {
                throw new RuntimeException('Aturan akses privat direktori dokumen MoU gagal dibuat.');
            }
            $accessControlRules = $denyRules;
        }

        if (
            $accessControlRules !== $denyRules
            || !chmod($accessControlPath, 0600)
        ) {
            throw new RuntimeException('Aturan akses langsung direktori dokumen MoU tidak tersedia atau tidak aman.');
        }
    }

    return $resolvedDirectory;
}

function perusahaanMouStoredFilePath(string $fileName, string $storageDirectory): ?string
{
    if (!preg_match('/^[a-f0-9]{64}\.(?:pdf|jpg|png|webp)$/D', $fileName)) {
        return null;
    }

    return $storageDirectory . DIRECTORY_SEPARATOR . $fileName;
}