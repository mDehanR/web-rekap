<?php
require_once 'functions.php';
cekLogin();
require_once 'db.php';

// ── Handle Delete ──
if (isset($_GET['hapus'])) {
    $delId = (int)$_GET['hapus'];
    $stmt  = $conn->prepare("DELETE FROM siswa_nilai WHERE id = ?");
    $stmt->bind_param("i", $delId);
    $stmt->execute();
    header("Location: rekap.php?deleted=1");
    exit();
}

// ── Fetch All ──
$sortCol = in_array($_GET['sort'] ?? '', ['nama_siswa', 'akhir', 'huruf', 'created_at']) ? $_GET['sort'] : 'created_at';
$sortDir = ($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
$nextDir = $sortDir === 'ASC' ? 'DESC' : 'ASC';

$result  = $conn->query("SELECT * FROM siswa_nilai ORDER BY $sortCol $sortDir");
$allData = [];
if ($result) {
    while ($row = $result->fetch_assoc()) $allData[] = $row;
}

// ── Statistik untuk export info ──
$totalSiswa = count($allData);
$avgAkhir   = $totalSiswa > 0 ? array_sum(array_column($allData, 'akhir')) / $totalSiswa : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rekap Nilai — Daftar Nilai</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">
  <link rel="stylesheet" href="style.css">
  <style>
    th.sortable { cursor: pointer; user-select: none; }
    th.sortable:hover { color: var(--accent); }
    th.sortable .sort-arrow { opacity: 0.4; font-size: 10px; margin-left: 4px; }
    th.sortable.active .sort-arrow { opacity: 1; color: var(--accent); }

    @media print {
      .sidebar, .bottom-nav, .mobile-header, .toolbar,
      .btn-action, .page-header .btn-primary, #noResult,
      .btn-primary, button { display: none !important; }
      .main-content { margin: 0 !important; padding: 1rem !important; }
      body { background: white !important; color: black !important; }
      .data-table th, .data-table td { color: black !important; border-color: #ccc !important; }
      .card { border: 1px solid #ccc !important; background: white !important; box-shadow: none !important; }
    }
  </style>
</head>
<body>
<div id="toastContainer" class="toast-container"></div>

<div class="dashboard-layout">
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <div class="mobile-title">📋 Rekap Nilai</div>
      <button class="hamburger" id="hamburger">☰</button>
    </div>

    <!-- Page Header -->
    <div class="page-header" data-reveal>
      <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
        <div>
          <h1 class="page-title">📋 Rekap Nilai Siswa</h1>
          <div class="page-breadcrumb"><span>Rekap Nilai</span> / Daftar Nilai</div>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
          <button onclick="window.print()" class="btn-action" style="border:1px solid var(--border); color:var(--text-muted); background:rgba(255,255,255,0.03); cursor:pointer;">
            🖨️ Cetak
          </button>
          <a href="input_nilai.php" class="btn-primary">
            ➕ Tambah Nilai
          </a>
        </div>
      </div>
    </div>

    <!-- Summary Strip -->
    <div style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;" data-stagger>
      <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.875rem 1.25rem; display:flex; gap:0.75rem; align-items:center; flex:1; min-width:160px;">
        <span style="font-size:1.25rem;">👨‍🎓</span>
        <div>
          <div style="font-size:1.4rem; font-weight:700; font-family:var(--font-display);" data-count="<?= $totalSiswa ?>">0</div>
          <div style="font-size:11px; color:var(--text-dim); text-transform:uppercase; letter-spacing:1px;">Total Siswa</div>
        </div>
      </div>
      <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.875rem 1.25rem; display:flex; gap:0.75rem; align-items:center; flex:1; min-width:160px;">
        <span style="font-size:1.25rem;">📊</span>
        <div>
          <div style="font-size:1.4rem; font-weight:700; font-family:var(--font-display); color:var(--accent);" data-count="<?= round($avgAkhir, 2) ?>" data-decimals="2">0</div>
          <div style="font-size:11px; color:var(--text-dim); text-transform:uppercase; letter-spacing:1px;">Rata-rata</div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="card" data-reveal>
      <div class="card-header">
        <h2 class="card-title">Daftar Nilai</h2>
        <span style="font-size:12px; color:var(--text-dim);"><?= $totalSiswa ?> siswa</span>
      </div>

      <div class="card-body" style="padding-bottom:0.5rem;">
        <!-- Search -->
        <div class="toolbar">
          <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input
              type="text"
              id="searchInput"
              class="search-input"
              placeholder="Cari nama siswa..."
              autocomplete="off"
            >
          </div>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="data-table" id="dataTable">
          <thead>
            <tr>
              <th style="width:40px;">#</th>
              <th class="sortable <?= $sortCol === 'nama_siswa' ? 'active' : '' ?>">
                <a href="?sort=nama_siswa&dir=<?= $sortCol === 'nama_siswa' ? $nextDir : 'ASC' ?>" style="color:inherit; text-decoration:none;">
                  Nama Siswa <span class="sort-arrow"><?= $sortCol === 'nama_siswa' ? ($sortDir === 'ASC' ? '↑' : '↓') : '↕' ?></span>
                </a>
              </th>
              <th>Tugas</th>
              <th>UTS</th>
              <th>UAS</th>
              <th class="sortable <?= $sortCol === 'akhir' ? 'active' : '' ?>">
                <a href="?sort=akhir&dir=<?= $sortCol === 'akhir' ? $nextDir : 'DESC' ?>" style="color:inherit; text-decoration:none;">
                  Nilai Akhir <span class="sort-arrow"><?= $sortCol === 'akhir' ? ($sortDir === 'ASC' ? '↑' : '↓') : '↕' ?></span>
                </a>
              </th>
              <th>Huruf</th>
              <th>Predikat</th>
              <th style="min-width:110px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($allData)): ?>
            <tr>
              <td colspan="9">
                <div class="empty-state">
                  <div class="empty-icon">📝</div>
                  <div class="empty-title">Belum ada data nilai</div>
                  <div class="empty-sub">
                    <a href="input_nilai.php" style="color:var(--accent);">Tambah nilai siswa pertama</a>
                  </div>
                </div>
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($allData as $i => $row):
              $badge = badgeWarna($row['huruf']);
            ?>
            <tr>
              <td style="color:var(--text-dim); font-size:12px;"><?= $i + 1 ?></td>
              <td class="name-cell"><?= e($row['nama_siswa']) ?></td>
              <td class="num-cell">
                <div class="progress-bar-wrap">
                  <?= number_format($row['tugas'], 1) ?>
                  <div class="progress-bar" style="max-width:50px;">
                    <div class="progress-fill" data-target="<?= $row['tugas'] ?>" style="width:0%;"></div>
                  </div>
                </div>
              </td>
              <td class="num-cell">
                <div class="progress-bar-wrap">
                  <?= number_format($row['uts'], 1) ?>
                  <div class="progress-bar" style="max-width:50px;">
                    <div class="progress-fill" data-target="<?= $row['uts'] ?>" style="background: var(--accent2); width:0%;"></div>
                  </div>
                </div>
              </td>
              <td class="num-cell">
                <div class="progress-bar-wrap">
                  <?= number_format($row['uas'], 1) ?>
                  <div class="progress-bar" style="max-width:50px;">
                    <div class="progress-fill" data-target="<?= $row['uas'] ?>" style="background: var(--success); width:0%;"></div>
                  </div>
                </div>
              </td>
              <td style="font-weight:700; font-size:15px; font-family:var(--font-display); color:var(--text-primary);">
                <?= number_format($row['akhir'], 2) ?>
              </td>
              <td><span class="badge <?= $badge ?>"><?= e($row['huruf']) ?></span></td>
              <td style="font-size:12px; color:var(--text-muted);"><?= e($row['predikat']) ?></td>
              <td>
                <div style="display:flex; gap:6px;">
                  <a href="input_nilai.php?edit=<?= $row['id'] ?>" class="btn-action edit">✏️</a>
                  <a
                    href="rekap.php?hapus=<?= $row['id'] ?>"
                    class="btn-action delete"
                    data-confirm="Yakin hapus nilai <?= e($row['nama_siswa']) ?>?"
                  >🗑️</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- No search result -->
        <div id="noResult" style="display:none;" class="empty-state" style="padding:2rem;">
          <div class="empty-icon">🔍</div>
          <div class="empty-title">Tidak ditemukan</div>
          <div class="empty-sub">Coba kata kunci lain</div>
        </div>

      </div><!-- /table-wrapper -->
    </div><!-- /card -->
  </div><!-- /main-content -->
</div>

<?php include 'bottom_nav.php'; ?>
<script src="main.js"></script>
</body>
</html>
