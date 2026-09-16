<?php
$page_title = "Data Penempatan PKL";
require_once "../backend/connection.php";
require_once "components/header.php";

$query = "SELECT p.*, s.nama AS nama_siswa, s.nisn, s.kelas, s.jurusan, pr.nama AS nama_perusahaan 
          FROM penempatan_pkl p 
          LEFT JOIN siswa s ON p.id_siswa = s.id 
          LEFT JOIN perusahaan pr ON p.id_perusahaan = pr.id 
          ORDER BY p.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-id-card-clip text-warning me-2"></i>Data Penempatan Praktik Kerja Lapangan (PKL)</h3>
        <p class="text-muted mb-0">Kelola penempatan siswa di perusahaan mitra, guru/instruktur pembimbing, dan periode PKL.</p>
    </div>
    <a href="form/penempatan-pkl-form.php" class="btn btn-warning text-dark fw-semibold">
        <i class="fa-solid fa-plus me-1"></i> Tambah Penempatan PKL
    </a>
</div>

<?php require_once "components/alerts.php"; ?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold text-secondary"><i class="fa-solid fa-table me-2"></i>Daftar Penempatan PKL (<?= mysqli_num_rows($result) ?> Data)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Siswa</th>
                    <th>Perusahaan Mitra</th>
                    <th>Pembimbing</th>
                    <th>Periode PKL</th>
                    <th>Status</th>
                    <th style="width: 160px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $badge_status = match($row['status_penempatan']) {
                            'Disetujui' => 'bg-primary',
                            'Selesai'   => 'bg-success',
                            default     => 'bg-secondary'
                        };
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_siswa'] ?? 'Siswa Tidak Ditemukan') ?></div>
                            <small class="text-muted"><?= htmlspecialchars($row['nisn'] ?? '-') ?> &bull; <?= htmlspecialchars($row['kelas'] ?? '-') ?> (<?= htmlspecialchars($row['jurusan'] ?? '-') ?>)</small>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_perusahaan'] ?? 'Perusahaan Tidak Ditemukan') ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['pembimbing']) ?></td>
                        <td>
                            <small class="d-block text-muted">
                                <i class="fa-regular fa-calendar-check text-success me-1"></i> <?= date('d M Y', strtotime($row['tanggal_mulai'])) ?>
                            </small>
                            <small class="d-block text-muted">
                                <i class="fa-regular fa-calendar-xmark text-danger me-1"></i> <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?>
                            </small>
                        </td>
                        <td><span class="badge <?= $badge_status ?>"><?= htmlspecialchars($row['status_penempatan']) ?></span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="form/penempatan-pkl-form.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning" title="Edit Penempatan">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../backend/form/penempatan_pkl/proses-hapus.php?id=<?= $row['id'] ?>" onclick="return confirmDelete('Hapus penempatan PKL untuk <?= addslashes(htmlspecialchars($row['nama_siswa'] ?? 'siswa')) ?>?');" class="btn btn-outline-danger" title="Hapus Penempatan">
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
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data penempatan PKL. Klik "Tambah Penempatan PKL" untuk menambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "components/footer.php"; ?>
