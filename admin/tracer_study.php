<?php
$page_title = "Data Tracer Study";
require_once "../backend/connection.php";
require_once "components/header.php";

$query = "SELECT t.*, s.nama AS nama_siswa, s.nisn, s.jurusan 
          FROM tracer_study t 
          LEFT JOIN siswa s ON t.id_siswa = s.id 
          ORDER BY t.tahun_lulus DESC, t.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-chart-line text-danger me-2"></i>Data Tracer Study Alumni</h3>
        <p class="text-muted mb-0">Pelacakan jejak lulusan SMK: bekerja, kuliah, wirausaha, atau mencari kerja.</p>
    </div>
    <a href="form/tracer-study-form.php" class="btn btn-danger">
        <i class="fa-solid fa-plus me-1"></i> Tambah Data Tracer
    </a>
</div>

<?php require_once "components/alerts.php"; ?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold text-secondary"><i class="fa-solid fa-table me-2"></i>Data Tracer Study (<?= mysqli_num_rows($result) ?> Data)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Alumni</th>
                    <th>Tahun Lulus</th>
                    <th>Aktivitas / Status</th>
                    <th>Nama Instansi / Usaha / Kampus</th>
                    <th>Pendapatan / Bulan</th>
                    <th style="width: 160px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $badge_status = match($row['status_alumni']) {
                            'Bekerja'       => 'bg-success',
                            'Kuliah'        => 'bg-primary',
                            'Wirausaha'     => 'bg-info text-dark',
                            'Mencari Kerja' => 'bg-secondary',
                            default         => 'bg-light text-dark'
                        };
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_siswa'] ?? 'Alumni Tidak Ditemukan') ?></div>
                            <small class="text-muted">NISN: <?= htmlspecialchars($row['nisn'] ?? '-') ?> &bull; <?= htmlspecialchars($row['jurusan'] ?? '-') ?></small>
                        </td>
                        <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($row['tahun_lulus']) ?></span></td>
                        <td><span class="badge <?= $badge_status ?>"><?= htmlspecialchars($row['status_alumni']) ?></span></td>
                        <td><?= !empty($row['nama_instansi']) ? htmlspecialchars($row['nama_instansi']) : '<span class="text-muted fst-italic">-</span>' ?></td>
                        <td>
                            <?php if (!empty($row['pendapatan_bulanan'])): ?>
                                <span class="fw-semibold text-success">Rp <?= number_format($row['pendapatan_bulanan'], 0, ',', '.') ?></span>
                            <?php else: ?>
                                <span class="text-muted fst-italic">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="form/tracer-study-form.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../backend/form/tracer_study/proses-hapus.php?id=<?= $row['id'] ?>" onclick="return confirmDelete('Hapus data Tracer Study <?= addslashes(htmlspecialchars($row['nama_siswa'] ?? 'alumni')) ?>?');" class="btn btn-outline-danger" title="Hapus Data">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data tracer study. Klik "Tambah Data Tracer" untuk menambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "components/footer.php"; ?>
