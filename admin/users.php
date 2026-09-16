<?php
$page_title = "Manajemen Pengguna";
require_once "../backend/connection.php";
require_once "components/header.php";

$query = "SELECT id_user, username, nama_lengkap, jabatan, created_at FROM users ORDER BY id_user DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-users-gear text-secondary me-2"></i>Manajemen Pengguna / Staf Humas</h3>
        <p class="text-muted mb-0">Kelola akun pengguna, hak akses, nama lengkap, dan jabatan staf Humas.</p>
    </div>
    <a href="form/users-form.php" class="btn btn-secondary">
        <i class="fa-solid fa-plus me-1"></i> Tambah Pengguna
    </a>
</div>

<?php require_once "components/alerts.php"; ?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold text-secondary"><i class="fa-solid fa-table me-2"></i>Daftar Pengguna (<?= mysqli_num_rows($result) ?> Data)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Jabatan</th>
                    <th>Terdaftar Pada</th>
                    <th style="width: 160px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                        </td>
                        <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($row['username']) ?></span></td>
                        <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($row['jabatan']) ?></span></td>
                        <td><small class="text-muted"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></small></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="form/users-form.php?id=<?= $row['id_user'] ?>" class="btn btn-outline-warning" title="Edit Pengguna">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../backend/form/users/proses-hapus.php?id_user=<?= $row['id_user'] ?>" onclick="return confirmDelete('Hapus akun pengguna <?= addslashes(htmlspecialchars($row['username'])) ?>?');" class="btn btn-outline-danger" title="Hapus Pengguna">
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
                        <td colspan="6" class="text-center text-muted py-4">Belum ada pengguna terdaftar. Klik "Tambah Pengguna" untuk menambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "components/footer.php"; ?>
