<?php
require_once "../../backend/connection.php";

$id = intval($_GET['id'] ?? 0);
$is_edit = $id > 0;
$data = [];

if ($is_edit) {
    $data = find_by_id('users', $id, 'id_user');
    if (!$data) {
        $_SESSION['flash_error'] = "Data pengguna tidak ditemukan!";
        header("Location: ../users.php");
        exit();
    }
}

$page_title = $is_edit ? "Edit Pengguna" : "Tambah Pengguna";
require_once "../components/header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fa-solid <?= $is_edit ? 'fa-user-pen text-warning' : 'fa-user-plus text-secondary' ?> me-2"></i>
                <?= $is_edit ? "Edit Data Pengguna" : "Tambah Pengguna Baru" ?>
            </h3>
            <a href="../users.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <?php require_once "../components/alerts.php"; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="../../backend/form/users/<?= $is_edit ? 'proses-edit.php' : 'proses-tambah.php' ?>" method="POST">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id_user" value="<?= $data['id_user'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required placeholder="Contoh: Ahmad Fauzi, S.Pd" value="<?= htmlspecialchars($data['nama_lengkap'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="username" name="username" required placeholder="admin_humas" value="<?= htmlspecialchars($data['username'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" required placeholder="Koordinator Humas / Staf" value="<?= htmlspecialchars($data['jabatan'] ?? 'Staf Humas') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            Password <?= $is_edit ? '<span class="text-muted small fw-normal">(Kosongkan jika tidak ingin mengubah)</span>' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" class="form-control" id="password" name="password" <?= $is_edit ? '' : 'required' ?> placeholder="Minimal 6 karakter..." minlength="6">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="../users.php" class="btn btn-light">Batal</a>
                        <button type="submit" name="submit" class="btn <?= $is_edit ? 'btn-warning' : 'btn-secondary' ?> px-4">
                            <i class="fa-solid fa-save me-1"></i> <?= $is_edit ? "Simpan Perubahan" : "Simpan Pengguna" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "../components/footer.php"; ?>
