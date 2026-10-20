<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();
if ($_SESSION['user']['role'] !== 'murid') { http_response_code(403); exit('Hanya murid yang dapat membuat pengaduan.'); }
$categories = getCategories($pdo);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $judul = trim((string)($_POST['judul'] ?? ''));
    $lokasi = trim((string)($_POST['lokasi'] ?? ''));
    $deskripsi = trim((string)($_POST['deskripsi'] ?? ''));
    $idKategori = filter_input(INPUT_POST, 'id_kategori', FILTER_VALIDATE_INT);
    $validCategory = false;
    foreach ($categories as $cat) { if ((int)$cat['id_kategori'] === (int)$idKategori) { $validCategory = true; break; } }
    if ($judul === '' || mb_strlen($judul) > 150 || $lokasi === '' || mb_strlen($lokasi) > 150 || $deskripsi === '' || !$validCategory) {
        $error = 'Lengkapi semua kolom dan pastikan kategori valid.';
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO pengaduan (id_user, id_kategori, judul, lokasi, deskripsi) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([(int)$_SESSION['user']['id_user'], (int)$idKategori, $judul, $lokasi, $deskripsi]);
            $id = (int)$pdo->lastInsertId();
            $history = $pdo->prepare("INSERT INTO riwayat_status (id_pengaduan, id_user, status, catatan) VALUES (?, ?, 'Menunggu', ?)");
            $history->execute([$id, (int)$_SESSION['user']['id_user'], 'Pengaduan dibuat']);
            $pdo->commit();
            redirect('detail.php?id=' . $id);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($e->getMessage());
            $error = 'Pengaduan gagal disimpan. Silakan coba lagi.';
        }
    }
}
$pageTitle = 'Buat Pengaduan'; $homePrefix = ''; $assetPrefix = '';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">FORMULIR LAPORAN</p><h1>Buat pengaduan</h1><p class="muted">Jelaskan masalah sarana sekolah dengan singkat dan jelas.</p></div></div>
<section class="panel form-panel">
  <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="stack-form">
    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
    <label>Judul pengaduan<input name="judul" maxlength="150" required placeholder="Contoh: Lampu kelas tidak menyala"></label>
    <label>Kategori<select name="id_kategori" required><option value="">Pilih kategori</option><?php foreach ($categories as $cat): ?><option value="<?= (int)$cat['id_kategori'] ?>"><?= e($cat['nama_kategori']) ?></option><?php endforeach; ?></select></label>
    <label>Lokasi sarana<input name="lokasi" maxlength="150" required placeholder="Contoh: Ruang X RPL"></label>
    <label>Deskripsi masalah<textarea name="deskripsi" rows="5" required placeholder="Ceritakan kondisi dan kapan masalah ditemukan..."></textarea></label>
    <div class="form-actions"><a class="btn btn-light" href="dashboard.php">Batal</a><button class="btn" type="submit">Kirim pengaduan</button></div>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
