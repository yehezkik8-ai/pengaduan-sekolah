<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('ID pengaduan tidak valid.'); }
$user = $_SESSION['user'];
$isStaff = in_array($user['role'], ['admin_it','petugas'], true);
$sql = 'SELECT p.*, k.nama_kategori, u.nama AS nama_pelapor FROM pengaduan p JOIN kategori k ON k.id_kategori=p.id_kategori JOIN users u ON u.id_user=p.id_user WHERE p.id_pengaduan=?';
$params = [$id];
if (!$isStaff) { $sql .= ' AND p.id_user=?'; $params[] = (int)$user['id_user']; }
$stmt = $pdo->prepare($sql); $stmt->execute($params); $item = $stmt->fetch();
if (!$item) { http_response_code(404); exit('Pengaduan tidak ditemukan atau Anda tidak memiliki akses.'); }
$error = '';
if ($isStaff && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $status = (string)($_POST['status'] ?? '');
    $catatan = trim((string)($_POST['catatan'] ?? ''));
    if (!in_array($status, ['Menunggu','Diproses','Selesai'], true) || mb_strlen($catatan) > 2000) {
        $error = 'Status atau catatan tidak valid.';
    } elseif ($status !== $item['status']) {
        $pdo->beginTransaction();
        try {
            $upd = $pdo->prepare('UPDATE pengaduan SET status=? WHERE id_pengaduan=?');
            $upd->execute([$status, $id]);
            $hist = $pdo->prepare('INSERT INTO riwayat_status (id_pengaduan,id_user,status,catatan) VALUES (?,?,?,?)');
            $hist->execute([$id, (int)$user['id_user'], $status, $catatan !== '' ? $catatan : null]);
            $pdo->commit();
            redirect('detail.php?id=' . $id);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($e->getMessage());
            $error = 'Status gagal diperbarui.';
        }
    }
}
$histStmt = $pdo->prepare('SELECT r.status,r.catatan,r.tanggal_perubahan,u.nama FROM riwayat_status r JOIN users u ON u.id_user=r.id_user WHERE r.id_pengaduan=? ORDER BY r.tanggal_perubahan DESC LIMIT 50');
$histStmt->execute([$id]); $history = $histStmt->fetchAll();
$pageTitle = 'Detail Pengaduan'; $homePrefix = ''; $assetPrefix = '';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">DETAIL LAPORAN #<?= (int)$item['id_pengaduan'] ?></p><h1><?= e($item['judul']) ?></h1><p class="muted">Dikirim oleh <?= e($item['nama_pelapor']) ?> pada <?= e(date('d M Y H:i', strtotime($item['tanggal_laporan']))) ?></p></div><a class="btn btn-light" href="dashboard.php">← Kembali</a></div>
<div class="detail-grid">
  <section class="panel">
    <div class="detail-top"><span class="badge <?= e(statusClass($item['status'])) ?>"><?= e($item['status']) ?></span><span class="muted small"><?= e($item['nama_kategori']) ?></span></div>
    <h3>Lokasi</h3><p><?= e($item['lokasi']) ?></p>
    <h3>Deskripsi</h3><p class="description"><?= nl2br(e($item['deskripsi'])) ?></p>
    <?php if ($isStaff): ?>
    <hr><h3>Perbarui status</h3>
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="stack-form">
      <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
      <label>Status baru<select name="status"><?php foreach (['Menunggu','Diproses','Selesai'] as $s): ?><option value="<?= e($s) ?>" <?= $item['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></label>
      <label>Catatan penanganan (opsional)<textarea name="catatan" rows="3" maxlength="2000"></textarea></label>
      <button class="btn" type="submit">Simpan perubahan</button>
    </form>
    <?php endif; ?>
  </section>
  <aside class="panel"><h2>Riwayat status</h2>
    <?php if (!$history): ?><p class="muted">Belum ada riwayat.</p><?php else: ?>
    <ol class="timeline"><?php foreach ($history as $h): ?><li><span class="timeline-dot"></span><div><strong><?= e($h['status']) ?></strong><small><?= e($h['nama']) ?> · <?= e(date('d M Y H:i', strtotime($h['tanggal_perubahan']))) ?></small><?php if ($h['catatan']): ?><p><?= nl2br(e($h['catatan'])) ?></p><?php endif; ?></div></li><?php endforeach; ?></ol>
    <?php endif; ?>
  </aside>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
