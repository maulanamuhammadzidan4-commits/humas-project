<?php
$page_title = "Data Lowongan Kerja";
require_once "../backend/connection.php";
require_once "components/header.php";

$query = "SELECT l.*, p.nama AS nama_perusahaan, p.sektor_bidang 
          FROM lowongan_kerja l 
          LEFT JOIN perusahaan p ON l.id_perusahaan = p.id 
          ORDER BY l.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-briefcase text-success me-2"></i>Data Lowongan Kerja (Loker)</h3>
        <p class="text-muted mb-0">Kelola info lowongan kerja industri untuk siswa dan alumni SMK.</p>
    </div>
    <a href="form/lowongan-kerja-form.php" class="btn btn-success">
        <i class="fa-solid fa-plus me-1"></i> Tambah Lowongan
    </a>
</div>

<?php require_once "components/alerts.php"; ?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold text-secondary"><i class="fa-solid fa-table me-2"></i>Daftar Lowongan Kerja (<?= mysqli_num_rows($result) ?> Data)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Posisi & Deskripsi</th>
                    <th>Perusahaan Penyedia</th>
                    <th>Kuota</th>
                    <th>Batas Pendaftaran</th>
                    <th>Status</th>
                    <th style="width: 160px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $is_open = ($row['status_loker'] === 'Buka');
                        $badge_class = $is_open ? 'bg-success' : 'bg-secondary';
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['posisi']) ?></div>
                            <small class="text-muted d-block text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($row['deskripsi']) ?>">
                                <?= htmlspecialchars($row['deskripsi']) ?>
                            </small>
                        </td>
                        <td>
                            <span class="fw-semibold text-primary"><?= htmlspecialchars($row['nama_perusahaan'] ?? 'Perusahaan Tidak Ditemukan') ?></span>
                            <?php if (!empty($row['sektor_bidang'])): ?>
                                <small class="text-muted d-block"><?= htmlspecialchars($row['sektor_bidang']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= intval($row['kuota']) ?> Orang</span></td>
                        <td>
                            <div><i class="fa-regular fa-calendar me-1 text-muted"></i><?= date('d M Y', strtotime($row['batas_daftar'])) ?></div>
                            <?php
                            $today = date('Y-m-d');
                            if ($row['batas_daftar'] < $today && $is_open):
                            ?>
                                <small class="text-danger fw-semibold"><i class="fa-solid fa-clock-rotate-left me-1"></i>Sudah lewat batas</small>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge <?= $badge_class ?>"><?= htmlspecialchars($row['status_loker']) ?></span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="form/lowongan-kerja-form.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning" title="Edit Lowongan">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../backend/form/lowongan_kerja/proses-hapus.php?id=<?= $row['id'] ?>" onclick="return confirmDelete('Hapus lowongan posisi <?= addslashes(htmlspecialchars($row['posisi'])) ?>?');" class="btn btn-outline-danger" title="Hapus Lowongan">
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
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data lowongan kerja. Klik "Tambah Lowongan" untuk membuat data baru.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "components/footer.php"; ?>
