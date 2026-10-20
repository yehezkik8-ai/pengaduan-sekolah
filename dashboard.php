<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();
$user = $_SESSION['user'];
$isStaff = in_array($user['role'], ['admin_it', 'petugas'], true);
$pageTitle = 'Dashboard'; $homePrefix = ''; $assetPrefix = '';
$statusFilter = trim((string)($_GET['status'] ?? ''));
$search = trim((string)($_GET['q'] ?? ''));
$search = mb_substr($search, 0, 80);
$complaints = getComplaints($pdo, (int)$user['id_user'], $isStaff, $statusFilter, $search, 50);
$counts = ['Menunggu' => 0, 'Diproses' => 0, 'Selesai' => 0];
if ($isStaff) {
    foreach ($pdo->query('SELECT status, COUNT(*) AS jumlah FROM pengaduan GROUP BY status')->fetchAll() as $row) {
        $counts[$row['status']] = (int)$row['jumlah'];
    }
}
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
  <div><p class="eyebrow">RUANG KERJA</p><h1>Dashboard</h1><p class="muted">Halo, <?= e($user['nama']) ?>. Berikut ringkasan pengaduan.</p></div>
  <?php if (!$isStaff): ?><a class="btn" href="tambah.php">+ Buat pengaduan</a><?php endif; ?>
</div>
<?php if ($isStaff): ?>
<div class="stats">
  <?php foreach ($counts as $label => $jumlah): ?>
  <div class="stat-card"><span><?= e($label) ?></span><strong><?= $jumlah ?></strong><small>pengaduan</small></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<section class="panel">
  <div class="panel-heading"><div><h2><?= $isStaff ? 'Semua pengaduan' : 'Pengaduan saya' ?></h2><p class="muted small">Maksimal 50 data terbaru ditampilkan.</p></div></div>
  <form class="filters" method="get">
    <input name="q" placeholder="Cari judul atau lokasi..." value="<?= e($search) ?>" maxlength="80">
    <select name="status" aria-label="Filter status">
      <option value="">Semua status</option>
      <?php foreach (['Menunggu','Diproses','Selesai'] as $s): ?>
      <option value="<?= e($s) ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= e($s) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-light" type="submit">Filter</button>
  </form>
  <?php if (!$complaints): ?>
    <div class="empty"><div class="empty-icon">⌕</div><h3>Belum ada pengaduan</h3><p class="muted">Laporan yang sesuai akan tampil di sini.</p></div>
  <?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th>Laporan</th><?php if ($isStaff): ?><th>Pelapor</th><?php endif; ?><th>Status</th><th>Tanggal</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($complaints as $item): ?>
      <tr>
        <td><strong><?= e($item['judul']) ?></strong><small><?= e($item['nama_kategori']) ?> · <?= e($item['lokasi']) ?></small></td>
        <?php if ($isStaff): ?><td><?= e($item['nama_pelapor']) ?></td><?php endif; ?>
        <td><span class="badge <?= e(statusClass($item['status'])) ?>"><?= e($item['status']) ?></span></td>
        <td><?= e(date('d M Y H:i', strtotime($item['tanggal_laporan']))) ?></td>
        <td><a class="table-link" href="detail.php?id=<?= (int)$item['id_pengaduan'] ?>">Detail →</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
