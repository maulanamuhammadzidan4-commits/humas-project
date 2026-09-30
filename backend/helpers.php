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