<?php
session_start();
// Redirect jika sudah login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: pilih_role.php");
    exit();
}

$error = '';
$success = '';
$mode = ($_GET['mode'] ?? 'login') === 'register' ? 'register' : 'login';

if (!isset($_SESSION['dummy_users']) || !is_array($_SESSION['dummy_users'])) {
    $_SESSION['dummy_users'] = [
        'guru' => 'guru123'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = ($_POST['mode'] ?? 'login') === 'register' ? 'register' : 'login';
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if ($mode === 'register') {
        if ($user === '' || $pass === '') {
            $error = 'Username dan password wajib diisi.';
        } elseif (isset($_SESSION['dummy_users'][$user])) {
            $error = 'Username sudah dipakai, gunakan username lain.';
        } else {
            $_SESSION['dummy_users'][$user] = $pass;
            $success = 'Sign In berhasil. Silakan login dengan akun baru Anda.';
            $mode = 'login';
        }
    } else {
        if (isset($_SESSION['dummy_users'][$user]) && $_SESSION['dummy_users'][$user] === $pass) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user;
            unset($_SESSION['role']);
            header("Location: pilih_role.php");
            exit();
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Rekap Nilai</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">
  <style>
    /* ================================================
       REKAP NILAI — Global Stylesheet
       Aesthetic: Refined Dark Academic / Slate Blue
       ================================================ */

    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap');

    /* ── CSS Variables ── */
    :root {
      --bg-deep:      #0a0e1a;
      --bg-card:      #111827;
      --bg-card2:     #1a2236;
      --accent:       #4f8ef7;
      --accent2:      #7c6af7;
      --accent3:      #f7c948;
      --success:      #34d399;
      --warning:      #fbbf24;
      --danger:       #f87171;
      --text-primary: #e8edf5;
      --text-muted:   #7b8599;
      --text-dim:     #4a5568;
      --border:       rgba(79, 142, 247, 0.15);
      --glow:         rgba(79, 142, 247, 0.25);
      --sidebar-w:    260px;
      --radius:       14px;
      --radius-sm:    8px;
      --font-display: 'Playfair Display', Georgia, serif;
      --font-body:    'DM Sans', sans-serif;
    }

    /* ── Reset & Base ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
      font-family: var(--font-body);
      background: var(--bg-deep);
      color: var(--text-primary);
      min-height: 100vh;
      overflow-x: hidden;
      font-size: 15px;
      line-height: 1.6;
    }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg-deep); }
    ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--accent); }

    /* ─────────────────────────────────────────
       LOGIN PAGE
    ───────────────────────────────────────── */
    .login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      position: relative;
      z-index: 10;
    }

    .login-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 2.5rem;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 25px 80px rgba(0,0,0,0.5), 0 0 60px rgba(79,142,247,0.08);
      animation: loginDrop 0.7s cubic-bezier(0.23, 1, 0.32, 1) both;
    }

    @keyframes loginDrop {
      from { opacity: 0; transform: translateY(-30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .login-logo {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-logo-icon {
      width: 64px; height: 64px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      border-radius: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 1rem;
      box-shadow: 0 0 30px rgba(79,142,247,0.4);
    }

    .login-title {
      font-family: var(--font-display);
      font-size: 1.6rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.25rem;
    }

    .login-sub {
      font-size: 13px;
      color: var(--text-muted);
    }

    .form-label {
      font-size: 12px;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 0.5rem;
      display: block;
    }

    .form-control-custom {
      width: 100%;
      background: rgba(255,255,255,0.04);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      color: var(--text-primary);
      padding: 12px 16px;
      font-size: 15px;
      font-family: var(--font-body);
      outline: none;
      transition: all 0.3s ease;
    }

    .form-control-custom::placeholder { color: var(--text-dim); }

    .form-control-custom:focus {
      border-color: var(--accent);
      background: rgba(79,142,247,0.06);
      box-shadow: 0 0 0 3px rgba(79,142,247,0.15);
    }

    .form-group { margin-bottom: 1.25rem; }

    .input-wrapper {
      position: relative;
    }

    .input-icon {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-dim);
      font-size: 16px;
    }

    .btn-login {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      border: none;
      border-radius: var(--radius-sm);
      font-size: 15px;
      font-weight: 600;
      font-family: var(--font-body);
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      box-shadow: 0 0 25px rgba(79,142,247,0.3);
      margin-top: 0.5rem;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 40px rgba(79,142,247,0.5);
    }

    .alert-error {
      background: rgba(248, 113, 113, 0.12);
      border: 1px solid rgba(248, 113, 113, 0.3);
      color: var(--danger);
      border-radius: var(--radius-sm);
      padding: 12px 16px;
      font-size: 14px;
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Animated background mesh */
    .mesh-bg {
      position: fixed;
      inset: 0;
      z-index: 0;
      overflow: hidden;
    }

    .mesh-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.35;
      animation: orbFloat 12s ease-in-out infinite;
    }

    .mesh-orb:nth-child(1) {
      width: 500px; height: 500px;
      background: radial-gradient(circle, #4f8ef7, transparent);
      top: -10%; left: -10%;
      animation-delay: 0s;
    }

    .mesh-orb:nth-child(2) {
      width: 400px; height: 400px;
      background: radial-gradient(circle, #7c6af7, transparent);
      bottom: -5%; right: -5%;
      animation-delay: -4s;
    }

    .mesh-orb:nth-child(3) {
      width: 300px; height: 300px;
      background: radial-gradient(circle, #f7c948, transparent);
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      animation-delay: -8s;
      opacity: 0.15;
    }

    @keyframes orbFloat {
      0%, 100% { transform: translate(0, 0) scale(1); }
      33%       { transform: translate(40px, -30px) scale(1.08); }
      66%       { transform: translate(-25px, 20px) scale(0.95); }
    }

    /* Grid overlay */
    .grid-overlay {
      position: fixed;
      inset: 0;
      z-index: 0;
      background-image:
        linear-gradient(rgba(79,142,247,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(79,142,247,0.04) 1px, transparent 1px);
      background-size: 60px 60px;
    }

    .welcome-body {
      background: var(--bg-deep);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      overflow: hidden;
      position: relative;
    }
  </style>
</head>
<body class="welcome-body">

  <!-- Background Effects -->
  <div class="mesh-bg">
    <div class="mesh-orb"></div>
    <div class="mesh-orb"></div>
    <div class="mesh-orb"></div>
  </div>
  <div class="grid-overlay"></div>

  <div class="login-wrapper">
    <div class="login-card">
      <div class="login-logo">
        <h1 class="login-title"><?= $mode === 'register' ? 'Buat Akun Dummy' : 'Selamat Datang' ?></h1>
        <p class="login-sub"><?= $mode === 'register' ? 'Sign In untuk mencoba akun baru' : 'Masuk dengan akun Anda' ?></p>
      </div>

      <?php if ($success): ?>
        <div style="background: rgba(52, 211, 153, 0.12); border: 1px solid rgba(52, 211, 153, 0.3); color: var(--success); border-radius: var(--radius-sm); padding: 12px 16px; font-size: 14px; margin-bottom: 1.25rem;">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert-error">
          <span>⚠</span>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="hidden" name="mode" value="<?= $mode ?>">
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <div class="input-wrapper">
            <input
              type="text"
              id="username"
              name="username"
              class="form-control-custom"
              placeholder="Masukkan username"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
              autocomplete="username"
              required
            >
            <span class="input-icon">👤</span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-wrapper">
            <input
              type="password"
              id="password"
              name="password"
              class="form-control-custom"
              placeholder="Masukkan password"
              autocomplete="current-password"
              required
            >
            <span class="input-icon" id="togglePass" style="cursor:pointer;" title="Tampilkan password">🔒</span>
          </div>
        </div>

        <button type="submit" class="btn-login">
          <?= $mode === 'register' ? 'Sign In Dummy' : 'Masuk ke Dashboard' ?>
        </button>
      </form>

      <div style="text-align:center; margin-top:1.5rem; font-size:12px; color:var(--text-dim);">
        Demo: <code style="background:rgba(255,255,255,0.05); padding:2px 6px; border-radius:4px; color:var(--accent);">guru</code> /
        <code style="background:rgba(255,255,255,0.05); padding:2px 6px; border-radius:4px; color:var(--accent);">guru123</code>
      </div>

      <div style="text-align:center; margin-top:1rem; font-size:13px;">
        <?php if ($mode === 'register'): ?>
          <a href="login.php?mode=login" style="color:var(--accent); text-decoration:none;">Sudah punya akun? Login</a>
        <?php else: ?>
          <a href="login.php?mode=register" style="color:var(--accent); text-decoration:none;">Belum punya akun? Sign In</a>
        <?php endif; ?>
      </div>

      <div style="text-align:center; margin-top:1rem;">
        <a href="index.php" style="font-size:13px; color:var(--text-dim); text-decoration:none;">
          ← Kembali ke halaman utama
        </a>
      </div>
    </div>
  </div>

  <script src="assets/js/main.js"></script>
  <script>
    // Toggle password visibility
    const togglePass = document.getElementById('togglePass');
    const passInput  = document.getElementById('password');
    if (togglePass && passInput) {
      togglePass.addEventListener('click', () => {
        const isText = passInput.type === 'text';
        passInput.type  = isText ? 'password' : 'text';
        togglePass.textContent = isText ? '🔒' : '👁';
      });
    }
  </script>
</body>
</html>
