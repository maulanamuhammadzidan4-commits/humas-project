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