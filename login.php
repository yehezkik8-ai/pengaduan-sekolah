<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';
if (!empty($_SESSION['user'])) { redirect('dashboard.php'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT id_user, nama, username, password, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        redirect('dashboard.php');
    }
    $error = 'Username atau password tidak sesuai.';
}
$pageTitle = 'Masuk';
$homePrefix = '';
$assetPrefix = '';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout">
  <div class="hero-copy">
    <p class="eyebrow">LINGKUNGAN SEKOLAH LEBIH BAIK</p>
    <h1>Sampaikan laporan.<br><span>Wujudkan perubahan.</span></h1>
    <p>Laporkan kerusakan sarana sekolah dan pantau proses penanganannya secara terstruktur.</p>
    <div class="feature-row"><span>✓ Pelaporan mudah</span><span>✓ Status transparan</span></div>
  </div>
  <section class="auth-card">
    <h2>Selamat datang</h2><p class="muted">Masuk ke akun Anda untuk melanjutkan.</p>
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
      <label>Username<input name="username" autocomplete="username" required maxlength="50"></label>
      <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
      <button class="btn" type="submit">Masuk</button>
    </form>
    <p class="small muted">Murid baru? <a href="daftar.php">Buat akun murid</a></p>
  </section>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
