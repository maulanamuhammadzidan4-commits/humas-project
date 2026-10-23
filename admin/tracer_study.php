
<?php
session_start();

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../backend/connection.php';

mysqli_set_charset($koneksi, 'utf8mb4');

$sql = "
    SELECT
        ts.id,
        s.nisn,
        s.nama_siswa,
        s.kelas,
        s.jurusan,
        ts.tahun_lulus,
        ts.status_alumni,
        ts.nama_instansi,
        ts.pendapatan_bulanan,
        ts.created_at
    FROM tracer_study ts
    INNER JOIN siswa s ON s.id = ts.siswa_id
    ORDER BY ts.created_at DESC, ts.id DESC
";

$result = mysqli_query($koneksi, $sql);

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Tracer Study - Admin Humas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #172033;
            padding: 20px;
        }
        .wrap {
            background: white;
            padding: 20px;
            border-radius: 12px;
            overflow-x: auto;
        }
        h1 { font-size: 24px; }
        input {
            padding: 10px;
            margin: 8px 0 16px;
            width: min(100%, 360px);
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 950px;
        }
        th, td {
            padding: 11px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th { background: #17365d; color: white; }
        .count { color: #64748b; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Data Tracer Study Alumni</h1>
    <p class="count">
        Total data: <?= mysqli_num_rows($result) ?> alumni
    </p>

    <input id="cari" type="search"
           placeholder="Cari nama, NISN, jurusan, atau status...">

    <table id="tabel">
        <thead>
        <tr>
            <th>No.</th>
            <th>Nama Alumni</th>
            <th>NISN</th>
            <th>Kelas</th>
            <th>Jurusan</th>
            <th>Tahun Lulus</th>
            <th>Status</th>
            <th>Instansi</th>
            <th>Pendapatan/Bulan</th>
            <th>Tanggal Isi</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($row['nama_siswa']) ?></td>
                <td><?= e($row['nisn']) ?></td>
                <td><?= e($row['kelas']) ?></td>
                <td><?= e($row['jurusan']) ?></td>
                <td><?= e($row['tahun_lulus']) ?></td>
                <td><?= e($row['status_alumni']) ?></td>
                <td><?= e($row['nama_instansi']) ?: '-' ?></td>
                <td>
                    <?php if ($row['pendapatan_bulanan'] !== null): ?>
                        Rp <?= number_format(
                            (int)$row['pendapatan_bulanan'],
                            0, ',', '.'
                        ) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?= e($row['created_at']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('cari').addEventListener('input', function () {
    const kata = this.value.toLowerCase();

    document.querySelectorAll('#tabel tbody tr').forEach(function (baris) {
        baris.hidden = !baris.textContent.toLowerCase().includes(kata);
    });
});
</script>
</body>
</html>
