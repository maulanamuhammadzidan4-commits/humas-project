<?php
/**
 * Backend Data Provider — Dashboard Admin Humas SMK
 * Menyediakan data statistik, lowongan expiring, penempatan PKL, dan tracer study.
 */

if (!isset($koneksi)) {
    require_once __DIR__ . '/../../backend/connection.php';
}

$stats = [];

/*
|--------------------------------------------------------------------------
| STATISTIK DASHBOARD
|--------------------------------------------------------------------------
*/
$queries = [
    // Jumlah perusahaan
    'perusahaan' => "SELECT COUNT(*) AS n FROM perusahaan",

    // Jumlah perusahaan dengan status MOU
    'mou_aktif' => "SELECT COUNT(*) AS n FROM perusahaan WHERE status_mou IS NOT NULL AND status_mou != ''",

    // Jumlah semua lowongan
    'lowongan' => "SELECT COUNT(*) AS n FROM lowongan_kerja",

    // Jumlah lowongan yang masih buka
    'loker_buka' => "SELECT COUNT(*) AS n FROM lowongan_kerja WHERE status_loker = 'Buka'",

    // Jumlah siswa
    'siswa' => "SELECT COUNT(*) AS n FROM siswa",

    // Jumlah alumni
    'alumni' => "SELECT COUNT(*) AS n FROM siswa WHERE status_alumni = 1",

    // Jumlah penempatan PKL
    'pkl' => "SELECT COUNT(*) AS n FROM pkl_penempatan",

    // Jumlah PKL yang disetujui
    'pkl_aktif' => "SELECT COUNT(*) AS n FROM pkl_penempatan WHERE status_penempatan = 'Disetujui'",

    // Jumlah data tracer study
    'tracer' => "SELECT COUNT(*) AS n FROM tracer_study",

    // Jumlah pengguna
    'users' => "SELECT COUNT(*) AS n FROM users"
];

foreach ($queries as $key => $sql) {
    $res = mysqli_query($koneksi, $sql);
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $stats[$key] = $row['n'] ?? 0;
    } else {
        $stats[$key] = 0;
    }
}

/*
|--------------------------------------------------------------------------
| LOWONGAN YANG AKAN SEGERA DITUTUP
|--------------------------------------------------------------------------
*/
$sql_expiring = "
    SELECT
        lk.judul_posisi AS posisi,
        p.nama_perusahaan AS perusahaan,
        lk.batas_pendaftaran,
        lk.status_loker
    FROM lowongan_kerja lk
    INNER JOIN perusahaan p ON p.id = lk.perusahaan_id
    WHERE lk.status_loker = 'Buka'
      AND lk.batas_pendaftaran BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY lk.batas_pendaftaran ASC
    LIMIT 5
";
$expiring = mysqli_query($koneksi, $sql_expiring);

/*
|--------------------------------------------------------------------------
| PKL TERBARU
|--------------------------------------------------------------------------
*/
$sql_pkl = "
    SELECT
        s.nama_siswa AS siswa,
        p.nama_perusahaan AS perusahaan,
        pk.pembimbing_guru,
        pk.status_penempatan,
        pk.tanggal_mulai,
        pk.tanggal_selesai
    FROM pkl_penempatan pk
    INNER JOIN siswa s ON s.id = pk.siswa_id
    INNER JOIN perusahaan p ON p.id = pk.perusahaan_id
    ORDER BY pk.created_at DESC
    LIMIT 6
";
$pkl_terbaru = mysqli_query($koneksi, $sql_pkl);

/*
|--------------------------------------------------------------------------
| DISTRIBUSI TRACER STUDY
|--------------------------------------------------------------------------
*/
$sql_tracer_dist = "
    SELECT status_alumni, COUNT(*) AS n
    FROM tracer_study
    GROUP BY status_alumni
";
$tracer_dist = mysqli_query($koneksi, $sql_tracer_dist);
$tracer_data = [];

if ($tracer_dist) {
    while ($r = mysqli_fetch_assoc($tracer_dist)) {
        $tracer_data[$r['status_alumni']] = $r['n'];
    }
}

// Default value tracer study
$tracer_data['Bekerja']       = $tracer_data['Bekerja'] ?? 0;
$tracer_data['Kuliah']        = $tracer_data['Kuliah'] ?? 0;
$tracer_data['Wirausaha']     = $tracer_data['Wirausaha'] ?? 0;
$tracer_data['Mencari Kerja'] = $tracer_data['Mencari Kerja'] ?? 0;
$tracer_data['Menikah']       = $tracer_data['Menikah'] ?? 0;
