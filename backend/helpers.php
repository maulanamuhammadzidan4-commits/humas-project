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