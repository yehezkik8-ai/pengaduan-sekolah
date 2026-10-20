<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';
if (!empty($_SESSION['user'])) redirect('dashboard.php');
$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $nama = trim((string)($_POST['nama'] ?? ''));
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($nama === '' || mb_strlen($nama) > 100 || !preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username) || strlen($password) < 8) {
        $error = 'Periksa nama, username (3–50 karakter: huruf/angka/._-), dan password minimal 8 karakter.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, 'murid')");
            $stmt->execute([$nama, $username, password_hash($password, PASSWORD_DEFAULT)]);
            $success = 'Akun berhasil dibuat. Silakan masuk.';
        } catch (PDOException $e) { $error = 'Username sudah digunakan.'; }
    }
}
$pageTitle = 'Daftar Murid'; $homePrefix = ''; $assetPrefix = '';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-card narrow">
  <p class="eyebrow">AKUN MURID</p><h1>Buat akun</h1>
  <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="success"><?= e($success) ?> <a href="login.php">Masuk</a></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
    <label>Nama lengkap<input name="nama" maxlength="100" required></label>
    <label>Username<input name="username" minlength="3" maxlength="50" required></label>
    <label>Password (minimal 8 karakter)<input type="password" name="password" minlength="8" required></label>
    <button class="btn" type="submit">Daftar</button>
  </form>
  <p class="small"><a href="login.php">Kembali ke login</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
