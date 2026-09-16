<?php
$page_title = "Data Siswa";
require_once "../backend/connection.php";
require_once "components/header.php";

$query = "SELECT * FROM siswa ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-user-graduate text-info me-2"></i>Data Siswa & Alumni</h3>
        <p class="text-muted mb-0">Kelola master data siswa SMK, kelas, kompetensi keahlian/jurusan, dan status alumni.</p>
    </div>
    <a href="form/siswa-form.php" class="btn btn-info text-white">
        <i class="fa-solid fa-plus me-1"></i> Tambah Siswa
    </a>
</div>

<?php require_once "components/alerts.php"; ?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold text-secondary"><i class="fa-solid fa-table me-2"></i>Daftar Siswa (<?= mysqli_num_rows($result) ?> Data)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>NISN</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Status</th>
                    <th style="width: 160px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $is_alumni = ($row['status_alumni'] == 1);
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($row['nisn']) ?></span></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['kelas']) ?></td>
                        <td><?= htmlspecialchars($row['jurusan']) ?></td>
                        <td>
                            <?php if ($is_alumni): ?>
                                <span class="badge bg-secondary">Alumni</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Siswa Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="form/siswa-form.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning" title="Edit Siswa">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../backend/form/siswa/proses-hapus.php?id=<?= $row['id'] ?>" onclick="return confirmDelete('Hapus siswa <?= addslashes(htmlspecialchars($row['nama'])) ?>? Tindakan ini juga akan menghapus data PKL dan Tracer Study terkait.');" class="btn btn-outline-danger" title="Hapus Siswa">
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
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data siswa. Klik "Tambah Siswa" untuk menambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "components/footer.php"; ?>
