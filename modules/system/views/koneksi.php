<section class="card wide">
  <h1>Koneksi database berhasil</h1>
  <p class="sub">Database <strong><?= e($dbName) ?></strong> | MySQL/MariaDB <?= e($versi) ?> | <?= date('d-m-Y H:i:s') ?></p>
  <table>
    <tr><th>Tabel</th><th>Jumlah baris</th></tr>
    <?php foreach ($tabel as $nama => $jumlah): ?>
      <tr><td><?= e($nama) ?></td><td><?= $jumlah ?></td></tr>
    <?php endforeach; ?>
  </table>
</section>
