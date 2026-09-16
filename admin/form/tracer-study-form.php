<?php
require_once "../../backend/connection.php";

$id = intval($_GET['id'] ?? 0);
$is_edit = $id > 0;
$data = [];

if ($is_edit) {
    $data = find_by_id('tracer_study', $id);
    if (!$data) {
        $_SESSION['flash_error'] = "Data tracer study tidak ditemukan!";
        header("Location: ../tracer_study.php");
        exit();
    }
}

// Ambil list siswa: jika edit, sertakan siswa saat ini; jika tambah, utamakan yang belum ada di tracer_study
if ($is_edit) {
    $siswa_list = mysqli_query($koneksi, "SELECT id, nisn, nama, jurusan FROM siswa WHERE id = " . intval($data['id_siswa']));
} else {
    $siswa_list = mysqli_query($koneksi, "SELECT s.id, s.nisn, s.nama, s.jurusan, s.status_alumni 
                                          FROM siswa s 
                                          LEFT JOIN tracer_study t ON s.id = t.id_siswa 
                                          WHERE t.id_siswa IS NULL 
                                          ORDER BY s.nama ASC");
}

$page_title = $is_edit ? "Edit Tracer Study" : "Tambah Tracer Study";
require_once "../components/header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fa-solid <?= $is_edit ? 'fa-pen-to-square text-warning' : 'fa-chart-line text-danger' ?> me-2"></i>
                <?= $is_edit ? "Edit Data Tracer Study" : "Tambah Data Tracer Study Alumni" ?>
            </h3>
            <a href="../tracer_study.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <?php require_once "../components/alerts.php"; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="../../backend/form/tracer_study/<?= $is_edit ? 'proses-edit.php' : 'proses-tambah.php' ?>" method="POST">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="id_siswa" class="form-label">Alumni / Siswa <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_siswa" name="id_siswa" required <?= $is_edit ? 'disabled' : '' ?>>
                            <option value="">-- Pilih Siswa / Alumni --</option>
                            <?php while ($s = mysqli_fetch_assoc($siswa_list)): ?>
                                <option value="<?= $s['id'] ?>" <?= (($data['id_siswa'] ?? 0) == $s['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['nama']) ?> (NISN: <?= htmlspecialchars($s['nisn']) ?> - <?= htmlspecialchars($s['jurusan']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if ($is_edit): ?>
                            <input type="hidden" name="id_siswa" value="<?= $data['id_siswa'] ?>">
                            <div class="form-text">Identitas alumni tidak dapat diubah saat mode edit.</div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tahun_lulus" class="form-label">Tahun Kelulusan <span class="text-danger">*</span></label>
                            <input type="number" min="1990" max="<?= date('Y') + 1 ?>" class="form-control" id="tahun_lulus" name="tahun_lulus" required value="<?= htmlspecialchars($data['tahun_lulus'] ?? date('Y')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status_alumni" class="form-label">Status / Aktivitas Saat Ini <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_alumni" name="status_alumni" required>
                                <option value="Bekerja" <?= (($data['status_alumni'] ?? '') === 'Bekerja') ? 'selected' : '' ?>>Bekerja (Karyawan / Pegawai)</option>
                                <option value="Kuliah" <?= (($data['status_alumni'] ?? '') === 'Kuliah') ? 'selected' : '' ?>>Kuliah (Pendidikan Tinggi)</option>
                                <option value="Wirausaha" <?= (($data['status_alumni'] ?? '') === 'Wirausaha') ? 'selected' : '' ?>>Wirausaha (Usaha Sendiri)</option>
                                <option value="Mencari Kerja" <?= (($data['status_alumni'] ?? 'Mencari Kerja') === 'Mencari Kerja') ? 'selected' : '' ?>>Mencari Kerja</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nama_instansi" class="form-label">Nama Tempat Bekerja / Kampus / Bidang Usaha</label>
                        <input type="text" class="form-control" id="nama_instansi" name="nama_instansi" placeholder="Contoh: PT Astra Honda Motor / Politeknik Negeri / Kedai Kopi" value="<?= htmlspecialchars($data['nama_instansi'] ?? '') ?>">
                        <div class="form-text">Boleh dikosongkan jika status masih mencari kerja.</div>
                    </div>

                    <div class="mb-4">
                        <label for="pendapatan_bulanan" class="form-label">Estimasi Pendapatan Bulanan (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" min="0" step="50000" class="form-control" id="pendapatan_bulanan" name="pendapatan_bulanan" placeholder="Contoh: 4500000" value="<?= htmlspecialchars($data['pendapatan_bulanan'] ?? '') ?>">
                        </div>
                        <div class="form-text">Opsional. Berguna untuk pelaporan statistik serapan kerja alumni.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="../tracer_study.php" class="btn btn-light">Batal</a>
                        <button type="submit" name="submit" class="btn <?= $is_edit ? 'btn-warning' : 'btn-danger' ?> px-4">
                            <i class="fa-solid fa-save me-1"></i> <?= $is_edit ? "Simpan Perubahan" : "Simpan Data Tracer" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "../components/footer.php"; ?>
