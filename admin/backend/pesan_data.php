<?php
/**
 * Backend Data Provider — Data Pesan Kontak Masuk
 */

if (!isset($koneksi)) {
    require_once __DIR__ . '/../../backend/connection.php';
}
require_once __DIR__ . '/../../backend/repositories/bootstrap.php';

$data = KontakRepository::getAll($koneksi);

$totalPesan  = count($data);
$belumDibaca = 0;
$sudahDibaca = 0;

foreach ($data as $row) {
    if ($row['status'] === 'Sudah Dibaca') {
        $sudahDibaca++;
    } else {
        $belumDibaca++;
    }
}
