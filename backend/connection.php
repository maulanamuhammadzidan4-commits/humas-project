<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$hostname = "localhost";
$username = "humas_user";
$password = "password_humas123";
$dbname = "db_humas_smk";

try {
    $koneksi = mysqli_connect(
        $hostname,
        $username,
        $password,
        $dbname
    );

    mysqli_set_charset($koneksi, "utf8mb4");

} catch (mysqli_sql_exception $e) {
    error_log("Koneksi database gagal: " . $e->getMessage());
    exit("Koneksi database gagal. Silakan hubungi administrator.");
}
