<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul ?? 'VOLTARA') ?> | VOLTARA</title>
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<header class="nav">
  <a class="brand" href="<?= url('home/index') ?>">VOLT<span>ARA</span></a>
  <nav>
    <?php if ($u = auth_user()): ?>
      <a href="<?= url('home/dashboard') ?>"><?= e($u['nama']) ?></a>
      <a class="btn small" href="<?= url('auth/logout') ?>">Keluar</a>
    <?php else: ?>
      <a href="<?= url('auth/register') ?>">Daftar</a>
      <a class="btn small" href="<?= url('auth/login') ?>">Masuk</a>
    <?php endif; ?>
  </nav>
</header>
<main class="wrap">
<?php foreach (flash_get() as $f): ?>
  <div class="alert <?= e($f['tipe']) ?>"><?= e($f['teks']) ?></div>
<?php endforeach; ?>
