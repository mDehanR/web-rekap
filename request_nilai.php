<?php
require_once 'functions.php';
cekRoleDipilih();

if (getRole() === 'guru') {
    redirect('beranda.php');
}

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_siswa'] ?? '');
    if ($nama !== '') {
        $_SESSION['request_nilai'][] = [
            'murid' => $_SESSION['username'] ?? 'murid',
            'nama_siswa' => $nama,
            'waktu' => date('Y-m-d H:i:s')
        ];
        $success = 'Request berhasil dikirim ke guru.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Nilai — Murid</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="dashboard-layout">
    <div class="main-content" style="margin-left:0; max-width:760px; width:100%; padding:2rem; margin-inline:auto;">
      <div class="page-header">
        <h1 class="page-title">Request Nilai ke Guru</h1>
        <div class="page-breadcrumb"><span>Murid</span> / Request Nilai</div>
      </div>

      <div class="card">
        <div class="card-body">
          <p style="color:var(--text-muted); margin-bottom:1rem;">
            Sebagai murid, Anda tidak dapat membuka rekap nilai secara langsung.
            Silakan kirim request ke guru.
          </p>

          <?php if ($success): ?>
          <div style="background: rgba(52, 211, 153, 0.12); border: 1px solid rgba(52, 211, 153, 0.3); color: var(--success); border-radius: 8px; padding: 10px 12px; margin-bottom:1rem;">
            <?= e($success) ?>
          </div>
          <?php endif; ?>

          <form method="POST">
            <div class="form-group" style="margin-bottom:1rem;">
              <label class="form-label" for="nama_siswa">Nama Siswa</label>
              <input type="text" id="nama_siswa" name="nama_siswa" class="form-control-custom" placeholder="Contoh: Budi Santoso" required>
            </div>
            <button type="submit" class="btn-primary">Kirim Request ke Guru</button>
          </form>
        </div>
      </div>

      <div style="margin-top:1rem;">
        <a href="pilih_role.php" style="color:var(--text-dim); text-decoration:none;">← Ganti Peran</a>
        <span style="margin-inline:8px; color:var(--text-dim);">|</span>
        <a href="logout.php" style="color:var(--danger); text-decoration:none;">Keluar</a>
      </div>
    </div>
  </div>
</body>
</html>
