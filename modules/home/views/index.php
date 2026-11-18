<section class="card wide">
  <h1>Masuk &amp; nikmati tarif bersih tanpa tekanan algoritma.</h1>
  <p class="sub">Halaman ini placeholder. Frontend akan mengganti dengan desain Figma.</p>
  <p><a class="btn small" href="<?= url('auth/register') ?>">Daftar</a>
     <a class="link" href="<?= url('auth/login') ?>">Masuk</a>
     <?php if (APP_DEBUG): ?> | <a class="link" href="<?= url('system/koneksi') ?>">Cek koneksi database</a><?php endif; ?></p>
</section>
