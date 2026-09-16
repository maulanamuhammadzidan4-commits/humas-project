<?php
$page_title = "Data Mitra Perusahaan";
require_once "../backend/connection.php";
require_once "components/header.php";

$query = "SELECT * FROM perusahaan ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-building text-primary me-2"></i>Data Mitra Industri / Perusahaan</h3>
        <p class="text-muted mb-0">Kelola informasi mitra kerja sama sekolah, kontak, dan status MoU.</p>
    </div>
    <a href="form/perusahaan-form.php" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Tambah Perusahaan
    </a>
</div>

<?php require_once "components/alerts.php"; ?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold text-secondary"><i class="fa-solid fa-table me-2"></i>Daftar Perusahaan (<?= mysqli_num_rows($result) ?> Data)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Perusahaan</th>
                    <th>Sektor / Bidang</th>
                    <th>Penanggung Jawab</th>
                    <th>Kontak</th>
                    <th>Status MoU</th>
                    <th style="width: 160px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $mou_badge = match($row['status_mou']) {
                            'Aktif' => 'bg-success',
                            'Kadaluarsa' => 'bg-danger',
                            default => 'bg-warning text-dark'
                        };
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></div>
                            <small class="text-muted d-block" style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?= htmlspecialchars($row['alamat']) ?>">
                                <i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($row['alamat']) ?>
                            </small>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['sektor_bidang']) ?></span></td>
                        <td><?= htmlspecialchars($row['penanggung_jawab']) ?></td>
                        <td>
                            <div><i class="fa-solid fa-envelope me-1 text-muted small"></i><?= htmlspecialchars($row['email']) ?></div>
                            <small class="text-muted"><i class="fa-solid fa-phone me-1 small"></i><?= htmlspecialchars($row['no_telepon']) ?></small>
                        </td>
                        <td><span class="badge <?= $mou_badge ?>"><?= htmlspecialchars($row['status_mou']) ?></span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="form/perusahaan-form.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../backend/form/perusahaan/proses-hapus.php?id=<?= $row['id'] ?>" onclick="return confirmDelete('Hapus data perusahaan <?= addslashes(htmlspecialchars($row['nama'])) ?>?');" class="btn btn-outline-danger" title="Hapus Data">
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
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data perusahaan. Klik tombol "Tambah Perusahaan" untuk menambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "components/footer.php"; ?>
