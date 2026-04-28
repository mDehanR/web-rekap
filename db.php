<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rekap_db');

// Suppress errors dan gunakan error handling manual
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli();
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 2);

if (!$conn->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
    error_log("Database connection failed: " . $conn->connect_error);
    // Buat dummy connection object untuk mencegah fatal error
    $conn = new stdClass();
    $conn->connect_error = true;
} else {
    $conn->set_charset("utf8mb4");

    // Pastikan tabel utama tersedia agar aplikasi tidak error saat pertama dijalankan.
    $createTableSql = "
    CREATE TABLE IF NOT EXISTS siswa_nilai (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        nama_siswa VARCHAR(100) NOT NULL,
        tugas FLOAT NOT NULL DEFAULT 0,
        uts FLOAT NOT NULL DEFAULT 0,
        uas FLOAT NOT NULL DEFAULT 0,
        akhir FLOAT NOT NULL DEFAULT 0,
        huruf VARCHAR(3) NOT NULL DEFAULT '',
        predikat VARCHAR(20) NOT NULL DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    if (!$conn->query($createTableSql)) {
        error_log("Gagal menyiapkan tabel siswa_nilai: " . $conn->error);
    }
}
?>
