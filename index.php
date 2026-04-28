<?php
session_start();
// Redirect ke beranda jika sudah login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'guru') {
      header("Location: beranda.php");
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'murid') {
      header("Location: request_nilai.php");
    } else {
      header("Location: login.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LastGrade - Rekap Nilai Siswa</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">
  <link rel="stylesheet" href="style.css">
</head>
<body class="welcome-body">

  <!-- Background Effects -->
  <div class="mesh-bg">
    <div class="mesh-orb"></div>
    <div class="mesh-orb"></div>
    <div class="mesh-orb"></div>
  </div>
  <div class="grid-overlay"></div>

  <!-- Hero Content -->
  <div class="hero-wrapper">
    <div data-reveal data-delay="1">
      <div class="hero-badge">
        <span>Sistem Akademik</span>
      </div>
    </div>

    <h1 class="hero-title" data-reveal data-delay="2">
      LastGrade Rekap Nilai Siswa
    </h1>

    <p class="hero-sub" data-reveal data-delay="3">
      Platform modern untuk mencatat, mengelola, dan menganalisis nilai siswa
      secara efisien dan akurat.
    </p>

    <div class="hero-buttons" data-reveal data-delay="4">
      <a href="login.php?mode=login" class="btn-primary-hero">
        <span>Login</span>
      </a>
      <a href="login.php?mode=register" class="btn-primary-hero">
        <span>Sign In</span>
      </a>
    </div>

    <div class="hero-stats" data-reveal data-delay="5">
      <div class="stat-item">
        <div class="stat-number">3</div>
        <div class="stat-label">Komponen Nilai</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <div class="stat-number">100%</div>
        <div class="stat-label">Otomatis</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <div class="stat-number">A–E</div>
        <div class="stat-label">Skala Huruf</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <div class="stat-number">📱</div>
        <div class="stat-label">Mobile Ready</div>
      </div>
    </div>
  </div>

  <script src="main.js"></script>
  <script>
    // Immediate reveal for hero (above fold)
    document.querySelectorAll('[data-reveal]').forEach(el => {
      el.classList.add('is-visible');
    });
  </script>
</body>
</html>
