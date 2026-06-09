<?php
require_once 'functions.php';
cekRoleGuru();
require_once 'db.php';

$errors  = [];
$success = false;
$editData = null;

// ── Mode Edit ──
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
if ($editId > 0) {
    $stmt = $conn->prepare("SELECT * FROM siswa_nilai WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editData = $stmt->get_result()->fetch_assoc();
    if (!$editData) $editId = 0;
}

// ── Process POST ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama_siswa'] ?? '');
    $tugas = (float)($_POST['tugas'] ?? 0);
    $uts   = (float)($_POST['uts']   ?? 0);
    $uas   = (float)($_POST['uas']   ?? 0);
    $mata  = trim($_POST['mata_pelajaran'] ?? '');
    $id    = (int)($_POST['id']      ?? 0);

    // Validasi
    if (empty($nama))         $errors[] = 'Nama siswa tidak boleh kosong.';
    if ($tugas < 0 || $tugas > 100) $errors[] = 'Nilai tugas harus antara 0–100.';
    if ($uts   < 0 || $uts   > 100) $errors[] = 'Nilai UTS harus antara 0–100.';
    if ($uas   < 0 || $uas   > 100) $errors[] = 'Nilai UAS harus antara 0–100.';

    if (empty($errors)) {
      if (empty($mata)) $errors[] = 'Pilih mata pelajaran.';
    }

    if (empty($errors)) {
        $akhir   = hitungNilaiAkhir($tugas, $uts, $uas);
        $huruf   = nilaiKeHuruf($akhir);
        $predikat= nilaiKePredikat($akhir);
      if ($id > 0) {
        // Update (include mata_pelajaran)
        $stmt = $conn->prepare("UPDATE siswa_nilai SET nama_siswa=?, mata_pelajaran=?, tugas=?, uts=?, uas=?, akhir=?, huruf=?, predikat=? WHERE id=?");
        $stmt->bind_param("ssddddssi", $nama, $mata, $tugas, $uts, $uas, $akhir, $huruf, $predikat, $id);
        $stmt->execute();
        header("Location: rekap.php?success=2");
        exit();
      } else {
        // Insert (include mata_pelajaran)
        $stmt = $conn->prepare("INSERT INTO siswa_nilai (nama_siswa, mata_pelajaran, tugas, uts, uas, akhir, huruf, predikat) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddddss", $nama, $mata, $tugas, $uts, $uas, $akhir, $huruf, $predikat);
        $stmt->execute();
        header("Location: rekap.php?success=1");
        exit();
      }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $editId ? 'Edit' : 'Input' ?> Nilai — Rekap Nilai</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>
<body>
<div id="toastContainer" class="toast-container"></div>

<div class="dashboard-layout">
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <div class="mobile-title">✏️ <?= $editId ? 'Edit' : 'Input' ?> Nilai</div>
      <button class="hamburger" id="hamburger">☰</button>
    </div>

    <!-- Page Header -->
    <div class="page-header" data-reveal>
      <h1 class="page-title"><?= $editId ? '✏️ Edit Nilai Siswa' : '✏️ Input Nilai Siswa' ?></h1>
      <div class="page-breadcrumb">
        <span>Rekap Nilai</span> / <?= $editId ? 'Edit Nilai' : 'Input Nilai' ?>
      </div>
    </div>

    <div style="max-width: 740px;">

      <!-- Error Messages -->
      <?php if (!empty($errors)): ?>
      <div data-reveal style="margin-bottom:1.5rem;">
        <?php foreach ($errors as $err): ?>
        <div class="alert-error" style="margin-bottom:0.5rem;">
          <span>⚠</span><span><?= e($err) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Form Card -->
      <div class="card" data-reveal data-delay="1">
        <div class="card-header">
          <h2 class="card-title"><?= $editId ? 'Perbarui Data Siswa' : 'Data Nilai Baru' ?></h2>
          <?php if ($editId): ?>
          <a href="input_nilai.php" class="btn-action" style="border:1px solid var(--border); color:var(--text-muted);">
            + Baru
          </a>
          <?php endif; ?>
        </div>

        <form method="POST" action="">
          <?php if ($editId): ?>
          <input type="hidden" name="id" value="<?= $editId ?>">
          <?php endif; ?>

          <div class="form-group" style="margin-bottom:1rem;">
            <label class="form-label" for="nama_siswa">Nama Lengkap Siswa</label>
            <input
              type="text"
              id="nama_siswa"
              name="nama_siswa"
              class="form-control-custom"
              placeholder="Contoh: Ahmad Rizky Pratama"
              value="<?= e($editData['nama_siswa'] ?? $_POST['nama_siswa'] ?? '') ?>"
              maxlength="100"
              required
              autocomplete="off"
            >
          </div>

          <div class="form-group" style="margin-bottom:1.5rem;">
            <label class="form-label" for="mata_pelajaran">Mata Pelajaran</label>
            <?php
              $matpels = ['Produktif','PAI','IPAS','Sejarah','Matematika','PJOK','PPKN','Seni Budaya','Basa Sunda','Inggris'];
              $current = $editData['mata_pelajaran'] ?? $_POST['mata_pelajaran'] ?? '';
            ?>
            <div class="matpel-buttons" role="list">
              <?php foreach ($matpels as $mp):
                $isActive = ($current === $mp) ? ' active' : '';
              ?>
              <div role="button" tabindex="0" class="matpel-button<?= $isActive ?>" data-value="<?= e($mp) ?>"><?= e($mp) ?></div>
              <?php endforeach; ?>
            </div>
            <input type="hidden" name="mata_pelajaran" id="mata_pelajaran" value="<?= e($current) ?>">
          </div>

          <!-- Nilai Grid -->
            <div class="form-row" style="margin-bottom:1.5rem;">
              <div class="form-group">
                <label class="form-label" for="tugas">
                  Nilai Tugas
                  <span style="color:var(--accent); font-size:10px; margin-left:4px;">× 30%</span>
                </label>
                <input
                  type="number"
                  id="tugas"
                  name="tugas"
                  class="form-control-custom"
                  placeholder="0 – 100"
                  value="<?= e($editData['tugas'] ?? $_POST['tugas'] ?? '') ?>"
                  min="0" max="100" step="0.5"
                  required
                >
              </div>

              <div class="form-group">
                <label class="form-label" for="uts">
                  Nilai UTS
                  <span style="color:var(--accent2); font-size:10px; margin-left:4px;">× 30%</span>
                </label>
                <input
                  type="number"
                  id="uts"
                  name="uts"
                  class="form-control-custom"
                  placeholder="0 – 100"
                  value="<?= e($editData['uts'] ?? $_POST['uts'] ?? '') ?>"
                  min="0" max="100" step="0.5"
                  required
                >
              </div>

              <div class="form-group">
                <label class="form-label" for="uas">
                  Nilai UAS
                  <span style="color:var(--success); font-size:10px; margin-left:4px;">× 40%</span>
                </label>
                <input
                  type="number"
                  id="uas"
                  name="uas"
                  class="form-control-custom"
                  placeholder="0 – 100"
                  value="<?= e($editData['uas'] ?? $_POST['uas'] ?? '') ?>"
                  min="0" max="100" step="0.5"
                  required
                >
              </div>
            </div>

            <!-- Preview Nilai Akhir -->
            <div class="calc-preview" data-reveal data-delay="2">
              <div>
                <div class="calc-preview-label">Nilai Akhir (Otomatis)</div>
                <div style="font-size:12px; color:var(--text-dim); margin-top:4px;">
                  (Tugas×30%) + (UTS×30%) + (UAS×40%)
                </div>
              </div>
              <div style="text-align:center;">
                <div class="calc-preview-value" id="previewNilai">0.00</div>
              </div>
              <div class="calc-preview-badge">
                <div style="font-size:2rem; font-weight:800; font-family:var(--font-display);" id="previewHuruf">—</div>
                <div style="font-size:11px; color:var(--text-dim);" id="previewPredikat">—</div>
              </div>
            </div>

            <!-- Submit Button -->
            <div style="margin-top:1.5rem; display:flex; gap:1rem; align-items:center;">
              <button type="submit" class="btn-primary" style="padding:12px 28px; font-size:14px;">
                <?= $editId ? '💾 Perbarui Data' : '➕ Simpan Nilai' ?>
              </button>
              <a href="rekap.php" style="font-size:14px; color:var(--text-muted); text-decoration:none;">
                ← Batal
              </a>
            </div>

          </form>
        </div>
      </div>

      <!-- Info Card -->
      <div class="card" style="margin-top:1.5rem;" data-reveal data-delay="3">
        <div class="card-body">
          <div style="font-size:13px; color:var(--text-muted); line-height:1.8;">
            <strong style="color:var(--text-primary);">ℹ️ Keterangan Nilai Huruf:</strong><br>
            <div style="display:flex; gap:0.75rem 1.5rem; flex-wrap:wrap; margin-top:0.75rem;">
              <?php
              $grades = [
                'A'  => ['≥ 90', '#34d399'],
                'B+' => ['80–89', '#7ab3ff'],
                'B'  => ['75–79', '#4f8ef7'],
                'C+' => ['70–74', '#fbbf24'],
                'C'  => ['65–69', '#e8ba30'],
                'D'  => ['55–64', '#ff9090'],
                'E'  => ['< 55',  '#f87171'],
              ];
              foreach ($grades as $g => [$range, $color]):
              ?>
              <span>
                <strong style="color:<?= $color ?>;"><?= $g ?></strong>
                <span style="color:var(--text-dim); font-size:12px;"> = <?= $range ?></span>
              </span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /max-width -->
  </div><!-- /main-content -->
</div>

<?php include 'bottom_nav.php'; ?>
<script src="main.js"></script>
</body>
</html>
