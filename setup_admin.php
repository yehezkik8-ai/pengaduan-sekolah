<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$count = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin_it'")->fetchColumn();
$error = '';
if ($count > 0) {
    http_response_code(403);
    exit('Admin IT sudah dibuat. Demi keamanan, hapus file setup_admin.php.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $nama = trim((string)($_POST['nama'] ?? ''));
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($nama === '' || !preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username) || strlen($password) < 10) {
        $error = 'Isi nama, username (3–50 karakter: huruf/angka/._-), dan password minimal 10 karakter.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nama, $username, password_hash($password, PASSWORD_DEFAULT), 'admin_it']);
            exit('Admin berhasil dibuat. Hapus file setup_admin.php sekarang, lalu login melalui login.php.');
        } catch (PDOException $e) {
            $error = 'Username sudah digunakan atau admin gagal dibuat.';
        }
    }
}
$pageTitle = 'Buat Admin Pertama';
$homePrefix = '';
$assetPrefix = '';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
  <p class="eyebrow">PENGATURAN AWAL</p><h1>Buat admin pertama</h1>
  <p>Halaman ini hanya berfungsi sebelum akun admin IT pertama dibuat. Hapus file ini setelah selesai.</p>
  <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
    <label>Nama<input name="nama" maxlength="100" required></label>
    <label>Username<input name="username" minlength="3" maxlength="50" required></label>
    <label>Password (minimal 10 karakter)<input type="password" name="password" minlength="10" required></label>
    <button class="btn" type="submit">Buat akun admin</button>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
