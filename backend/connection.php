<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "db_humas_smk";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $koneksi = mysqli_connect($hostname, $username, $password, $dbname);
} catch (mysqli_sql_exception $e) {
    echo "Koneksi database gagal: " . $e->getMessage();
    exit();
}