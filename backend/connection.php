
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$hostname = "localhost";
$username = "root";
$password = "";
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
    exit("Koneksi database gagal. Periksa konfigurasi MySQL Laragon.");
}

function show_all(string $table) {
    global $koneksi;

    $query = "SELECT * FROM `$table`";
    $raw_result = mysqli_query($koneksi, $query);
    $result = mysqli_fetch_all($raw_result, MYSQLI_ASSOC);

    mysqli_free_result($raw_result);

    return $result;
}

function show_where(string $table, string $where) {
    global $koneksi;

    $query = "SELECT * FROM `$table` WHERE $where";
    $raw_result = mysqli_query($koneksi, $query);
    $result = mysqli_fetch_all($raw_result, MYSQLI_ASSOC);

    mysqli_free_result($raw_result);

    return $result;
}
