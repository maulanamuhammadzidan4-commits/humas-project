<?php
$hostname = "localhost";
$username = "dev";
$password = "";
$dbname = "db_humas_smk";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $koneksi = mysqli_connect($hostname, $username, $password, $dbname);
} catch (mysqli_sql_exception $e) {
    echo "Koneksi database gagal: " . $e->getMessage();
    exit();
}

function show_all(string $table){
    global $koneksi;
    $query = "SELECT * FROM `$table`";
    $raw_result = mysqli_query($koneksi, $query);
    $result = $raw_result ? mysqli_fetch_all($raw_result, MYSQLI_ASSOC) : [];
    mysqli_free_result($raw_result);

    return $result;
}

function show_where(string $table, string $where){
    global $koneksi;
    $query = "SELECT * FROM `$table` WHERE `$where`";
    $raw_result = mysqli_query($koneksi, $query);
    $result = $raw_result ? mysqli_fetch_all($raw_result, MYSQLI_ASSOC) : [];
    mysqli_free_result($raw_result);

    return $result;
}