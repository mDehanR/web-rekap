<?php
/**
 * Hitung nilai akhir berbobot
 * Tugas: 30%, UTS: 30%, UAS: 40%
 */
function hitungNilaiAkhir($tugas, $uts, $uas) {
    return ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
}

/**
 * Konversi nilai angka ke huruf
 */
function nilaiKeHuruf($nilai) {
    if ($nilai >= 90) return 'A';
    if ($nilai >= 80) return 'B+';
    if ($nilai >= 75) return 'B';
    if ($nilai >= 70) return 'C+';
    if ($nilai >= 65) return 'C';
    if ($nilai >= 55) return 'D';
    return 'E';
}

/**
 * Konversi nilai angka ke predikat
 */
function nilaiKePredikat($nilai) {
    if ($nilai >= 90) return 'Sangat Baik';
    if ($nilai >= 75) return 'Baik';
    if ($nilai >= 65) return 'Cukup';
    if ($nilai >= 55) return 'Kurang';
    return 'Sangat Kurang';
}

/**
 * Badge warna berdasarkan nilai huruf
 */
function badgeWarna($huruf) {
    switch ($huruf) {
        case 'A':   return 'badge-a';
        case 'B+':  return 'badge-bplus';
        case 'B':   return 'badge-b';
        case 'C+':  return 'badge-cplus';
        case 'C':   return 'badge-c';
        case 'D':   return 'badge-d';
        default:    return 'badge-e';
    }
}

/**
 * Escape HTML untuk mencegah XSS
 */
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Cek session login
 */
function cekLogin() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        redirect('login.php');
    }
}

/**
 * Ambil role user dari session
 */
function getRole() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    return $_SESSION['role'] ?? null;
}

/**
 * Wajib pilih role setelah login
 */
function cekRoleDipilih() {
    cekLogin();
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['guru', 'murid'], true)) {
        redirect('login.php');
    }
}

/**
 * Hanya role guru yang boleh akses halaman ini
 */
function cekRoleGuru() {
    cekRoleDipilih();
    if ($_SESSION['role'] !== 'guru') {
        redirect('request_nilai.php');
    }
}
?>
