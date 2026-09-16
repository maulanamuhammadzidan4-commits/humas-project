<?php
if (isset($_SESSION['flash_success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check fs-5"></i>
        <div>' . htmlspecialchars($_SESSION['flash_success']) . '</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset($_SESSION['flash_success']);
}

if (isset($_SESSION['flash_error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
        <div>' . htmlspecialchars($_SESSION['flash_error']) . '</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset($_SESSION['flash_error']);
}
?>
