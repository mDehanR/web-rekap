<!-- ── SIDEBAR OVERLAY (mobile) ── -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── SIDEBAR ── -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">📊</div>
    <div>
      <div class="sidebar-app-name">Rekap Nilai</div>
      <div class="sidebar-app-sub">Akademik Digital</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Menu Utama</div>

    <a href="beranda.php" class="nav-item" data-page="beranda">
      <span class="nav-icon">🏠</span>
      Beranda
    </a>

    <a href="input_nilai.php" class="nav-item" data-page="input_nilai">
      <span class="nav-icon">✏️</span>
      Input Nilai
    </a>

    <a href="rekap.php" class="nav-item" data-page="rekap">
      <span class="nav-icon">📋</span>
      Rekap Nilai
    </a>

    <div class="nav-section-label" style="margin-top: 2rem;">Akun</div>

    <a href="logout.php" class="nav-item" style="color: var(--danger);">
      <span class="nav-icon">🚪</span>
      Keluar
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar">G</div>
      <div>
        <div class="user-name">Guru</div>
        <div class="user-role">Administrator</div>
      </div>
      <a href="logout.php" class="btn-logout" title="Keluar">🚪</a>
    </div>
  </div>
</aside>
