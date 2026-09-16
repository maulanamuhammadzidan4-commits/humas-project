<?php
require_once "../../backend/connection.php";

$id = intval($_GET['id'] ?? 0);
$is_edit = $id > 0;
$data = [];

if ($is_edit) {
    $data = find_by_id('siswa', $id);
    if (!$data) {
        $_SESSION['flash_error'] = "Data siswa tidak ditemukan!";
        header("Location: ../siswa.php");
        exit();
    }
}

$page_title = $is_edit ? "Edit Siswa" : "Tambah Siswa";
require_once "../components/header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fa-solid <?= $is_edit ? 'fa-pen-to-square text-warning' : 'fa-user-graduate text-info' ?> me-2"></i>
                <?= $is_edit ? "Edit Data Siswa" : "Tambah Siswa Baru" ?>
            </h3>
            <a href="../siswa.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <?php require_once "../components/alerts.php"; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="../../backend/form/siswa/<?= $is_edit ? 'proses-edit.php' : 'proses-tambah.php' ?>" method="POST">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nisn" class="form-label">NISN (Nomor Induk Siswa Nasional) <span class="text-danger">*</span></label>
                        <input type="text" maxlength="10" class="form-control font-monospace" id="nisn" name="nisn" required placeholder="10 digit NISN, contoh: 0071234567" value="<?= htmlspecialchars($data['nisn'] ?? '') ?>">
                        <div class="form-text">Maksimal 10 karakter unik.</div>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" required placeholder="Contoh: Muhammad Farhan" value="<?= htmlspecialchars($data['nama'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kelas" class="form-label">Kelas Saat Ini / Terakhir <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kelas" name="kelas" required placeholder="Contoh: XII RPL 1" value="<?= htmlspecialchars($data['kelas'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jurusan" class="form-label">Jurusan / Kompetensi Keahlian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jurusan" name="jurusan" required placeholder="Contoh: Rekayasa Perangkat Lunak" value="<?= htmlspecialchars($data['jurusan'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status_alumni" class="form-label">Status Siswa <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_alumni" name="status_alumni" required>
                            <option value="0" <?= (($data['status_alumni'] ?? 0) == 0) ? 'selected' : '' ?>>Siswa Aktif</option>
                            <option value="1" <?= (($data['status_alumni'] ?? 0) == 1) ? 'selected' : '' ?>>Alumni (Telah Lulus)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="../siswa.php" class="btn btn-light">Batal</a>
                        <button type="submit" name="submit" class="btn <?= $is_edit ? 'btn-warning' : 'btn-info text-white' ?> px-4">
                            <i class="fa-solid fa-save me-1"></i> <?= $is_edit ? "Simpan Perubahan" : "Simpan Data Siswa" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "../components/footer.php"; ?>
