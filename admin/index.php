<?php
session_start();
require_once '../backend/connection.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
header("Location: login_admin.php");
exit;
