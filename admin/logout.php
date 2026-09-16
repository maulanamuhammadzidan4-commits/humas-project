<?php
session_start();
require_once '../backend/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_admin.php");
    exit;
}
if (isset($_SESSION['login_success_flash'])) {
    unset($_SESSION['login_success_flash']);
}

session_destroy();
header("Location: login_admin.php?logout=1");
exit;
