<?php
/**
 * Backend Data Provider — Data Pesan Kontak Masuk
 */

if (!isset($koneksi)) {
    require_once __DIR__ . '/../../backend/connection.php';
}

$sql = "
    SELECT
        id_kontak,
        nama,
        email,
        subjek,
        pesan,
        tanggal_kirim,
        status
    FROM kontak
    ORDER BY tanggal_kirim DESC
";

$result = mysqli_query($koneksi, $sql);

if (!$result) {
    die("Gagal mengambil data pesan: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
