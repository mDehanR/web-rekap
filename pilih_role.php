<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    if ($role === 'guru' || $role === 'murid') {
        $_SESSION['role'] = $role;
        if ($role === 'guru') {
            header('Location: beranda.php');
        } else {
            header('Location: request_nilai.php');
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Peran — Rekap Nilai</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="welcome-body">
  <div class="mesh-bg">
    <div class="mesh-orb"></div>
    <div class="mesh-orb"></div>
    <div class="mesh-orb"></div>
  </div>
  <div class="grid-overlay"></div>

  <div class="hero-wrapper">
    <h1 class="hero-title">Pilih Peran Anda</h1>
    <p class="hero-sub">Pilih sebagai Guru atau Murid sebelum melanjutkan.</p>

    <form method="POST" style="display:flex; gap:1rem; flex-wrap:wrap; justify-content:center;">
      <button class="btn-primary-hero" type="submit" name="role" value="guru">Guru</button>
      <button class="btn-primary-hero" type="submit" name="role" value="murid">Murid</button>
    </form>

    <div style="margin-top: 1rem; text-align:center;">
      <a href="logout.php" style="color: var(--text-dim); text-decoration:none;">Keluar akun</a>
    </div>
  </div>
</body>
</html>
