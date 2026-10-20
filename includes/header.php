<?php
$pageTitle = $pageTitle ?? 'Pengaduan Sarana Sekolah';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> · SaranaSekolah</title>
  <link rel="stylesheet" href="<?= e($assetPrefix ?? '') ?>assets/css/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= e($homePrefix ?? '') ?>index.php"><span class="brand-mark">S</span><span>SaranaSekolah</span></a>
  <nav>
    <?php if (!empty($_SESSION['user'])): ?>
      <span class="nav-user"><?= e($_SESSION['user']['nama']) ?> · <?= e($_SESSION['user']['role']) ?></span>
      <a href="<?= e($homePrefix ?? '') ?>dashboard.php">Dashboard</a>
      <form class="inline-form" method="post" action="<?= e($homePrefix ?? '') ?>logout.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <button class="link-button" type="submit">Keluar</button>
      </form>
    <?php else: ?>
      <a href="<?= e($homePrefix ?? '') ?>login.php">Masuk</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
