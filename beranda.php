<?php
require_once 'functions.php';
cekLogin();
require_once 'db.php';

// ── Statistik ──
$totalSiswa = $conn->query("SELECT COUNT(*) AS n FROM siswa_nilai")->fetch_assoc()['n'] ?? 0;
$avgAkhir   = $conn->query("SELECT AVG(akhir) AS a FROM siswa_nilai")->fetch_assoc()['a'] ?? 0;
$nilaiTert  = $conn->query("SELECT MAX(akhir) AS m FROM siswa_nilai")->fetch_assoc()['m'] ?? 0;
$nilaiTerend= $conn->query("SELECT MIN(akhir) AS m FROM siswa_nilai")->fetch_assoc()['m'] ?? 0;

// ── Distribusi Huruf ──
$distribusi = [];
$res = $conn->query("SELECT huruf, COUNT(*) AS jumlah FROM siswa_nilai GROUP BY huruf ORDER BY huruf");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $distribusi[$row['huruf']] = $row['jumlah'];
    }
}

// ── Data terbaru ──
$recent = $conn->query("SELECT * FROM siswa_nilai ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beranda — Rekap Nilai</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div id="toastContainer" class="toast-container"></div>

<div class="dashboard-layout">
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <div class="mobile-title">📊 Beranda</div>
      <button class="hamburger" id="hamburger">☰</button>
    </div>

    <!-- Page Header -->
    <div class="page-header" data-reveal>
      <h1 class="page-title">Dashboard</h1>
      <div class="page-breadcrumb"><span>Rekap Nilai</span> / Beranda</div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" data-stagger>
      <div class="stat-card blue">
        <div class="stat-card-icon">👨‍🎓</div>
        <div class="stat-card-value" data-count="<?= $totalSiswa ?>">0</div>
        <div class="stat-card-label">Total Siswa</div>
      </div>

      <div class="stat-card green">
        <div class="stat-card-icon">📈</div>
        <div class="stat-card-value" data-count="<?= round($avgAkhir, 1) ?>" data-decimals="1">0</div>
        <div class="stat-card-label">Rata-rata Nilai</div>
      </div>

      <div class="stat-card yellow">
        <div class="stat-card-icon">🏆</div>
        <div class="stat-card-value" data-count="<?= round($nilaiTert, 1) ?>" data-decimals="1">0</div>
        <div class="stat-card-label">Nilai Tertinggi</div>
      </div>

      <div class="stat-card purple">
        <div class="stat-card-icon">📉</div>
        <div class="stat-card-value" data-count="<?= round($nilaiTerend, 1) ?>" data-decimals="1">0</div>
        <div class="stat-card-label">Nilai Terendah</div>
      </div>
    </div>

    <!-- Row: Distribusi + Recent -->
    <div style="display:grid; grid-template-columns: 340px 1fr; gap:1.5rem; flex-wrap:wrap;" id="mainGrid">

      <!-- Distribusi Huruf -->
      <div class="card" data-reveal="left">
        <div class="card-header">
          <h2 class="card-title">📊 Distribusi Nilai</h2>
        </div>
        <div class="card-body">
          <?php
          $gradeOrder = ['A', 'B+', 'B', 'C+', 'C', 'D', 'E'];
          $gradeColors = [
            'A'  => '#34d399',
            'B+' => '#7ab3ff',
            'B'  => '#4f8ef7',
            'C+' => '#fbbf24',
            'C'  => '#e8ba30',
            'D'  => '#ff9090',
            'E'  => '#f87171',
          ];
          foreach ($gradeOrder as $grade):
            $count = $distribusi[$grade] ?? 0;
            $pct   = $totalSiswa > 0 ? round(($count / $totalSiswa) * 100) : 0;
            $color = $gradeColors[$grade];
          ?>
          <div style="margin-bottom:1.1rem;" data-reveal data-delay="<?= array_search($grade, $gradeOrder) + 1 ?>">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
              <span style="font-size:13px; font-weight:600; color:<?= $color ?>;">Nilai <?= e($grade) ?></span>
              <span style="font-size:12px; color:var(--text-dim);"><?= $count ?> siswa (<?= $pct ?>%)</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" data-target="<?= $pct ?>" style="background: <?= $color ?>; width:0%;"></div>
            </div>
          </div>
          <?php endforeach; ?>

          <?php if ($totalSiswa === 0): ?>
          <div class="empty-state" style="padding: 2rem 1rem;">
            <div class="empty-icon">📊</div>
            <div class="empty-title">Belum ada data</div>
            <div class="empty-sub">Tambah nilai siswa terlebih dahulu</div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Data Terbaru -->
      <div class="card" data-reveal="right">
        <div class="card-header">
          <h2 class="card-title">🕐 Input Terbaru</h2>
          <a href="rekap.php" class="btn-primary" style="font-size:12px; padding:7px 14px;">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
          <table class="data-table" id="dataTable">
            <thead>
              <tr>
                <th>Nama Siswa</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Akhir</th>
                <th>Grade</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recent && $recent->num_rows > 0):
                while ($row = $recent->fetch_assoc()):
                  $huruf  = e($row['huruf']);
                  $badge  = badgeWarna($row['huruf']);
              ?>
              <tr>
                <td class="name-cell"><?= e($row['nama_siswa']) ?></td>
                <td class="num-cell"><?= number_format($row['tugas'], 1) ?></td>
                <td class="num-cell"><?= number_format($row['uts'], 1) ?></td>
                <td class="num-cell"><?= number_format($row['uas'], 1) ?></td>
                <td class="num-cell" style="font-weight:700;"><?= number_format($row['akhir'], 2) ?></td>
                <td><span class="badge <?= $badge ?>"><?= $huruf ?></span></td>
              </tr>
              <?php endwhile; else: ?>
              <tr>
                <td colspan="6">
                  <div class="empty-state">
                    <div class="empty-icon">📝</div>
                    <div class="empty-title">Belum ada data</div>
                    <div class="empty-sub"><a href="input_nilai.php" style="color:var(--accent);">Input nilai pertama</a></div>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Rumus Info Card -->
    <div class="card" style="margin-top:1.5rem;" data-reveal>
      <div class="card-body">
        <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;">
          <div style="font-size:2rem;">🔢</div>
          <div style="flex:1;">
            <div style="font-family:var(--font-display); font-size:1.1rem; font-weight:700; margin-bottom:0.25rem;">Rumus Perhitungan Nilai Akhir</div>
            <div style="font-size:14px; color:var(--text-muted);">
              Nilai Akhir = <strong style="color:var(--accent);">(Tugas × 30%)</strong> +
              <strong style="color:var(--accent2);">(UTS × 30%)</strong> +
              <strong style="color:var(--success);">(UAS × 40%)</strong>
            </div>
          </div>
          <div style="display:flex; gap:1.5rem; flex-wrap:wrap;">
            <?php foreach(['Tugas' => '30%', 'UTS' => '30%', 'UAS' => '40%'] as $k => $v): ?>
            <div style="text-align:center;">
              <div style="font-family:var(--font-display); font-size:1.5rem; color:var(--accent);"><?= $v ?></div>
              <div style="font-size:11px; color:var(--text-dim); text-transform:uppercase; letter-spacing:1px;"><?= $k ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /.main-content -->
</div>

<?php include 'bottom_nav.php'; ?>
<script src="main.js"></script>
<script>
  // Fix grid responsive
  const grid = document.getElementById('mainGrid');
  function fixGrid() {
    grid.style.gridTemplateColumns = window.innerWidth < 900 ? '1fr' : '340px 1fr';
  }
  fixGrid();
  window.addEventListener('resize', fixGrid);
</script>
</body>
</html>
