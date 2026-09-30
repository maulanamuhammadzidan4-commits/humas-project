<?php
/**
 * Auth Guard — wajib di-include di setiap halaman admin.
 * Redirect ke login jika session tidak aktif.
 */
require_once __DIR__ . '/../../backend/helpers.php';

$authLoginPath = $authLoginPath ?? 'login_admin.php';
ensureAdminAuthenticated($authLoginPath);
