<section class="card">
  <h1>Masukkan 4 digit kode OTP</h1>
  <p class="sub">Kode berlaku <?= (int) (app_config('otp_ttl') / 60) ?> menit.</p>
  <form method="post" action="<?= url('auth/verify-otp') ?>">
    <?= csrf_field() ?>
    <input class="otp" name="kode" maxlength="4" inputmode="numeric" pattern="\d{4}" autocomplete="one-time-code" required autofocus>
    <button class="btn" type="submit">Verifikasi &amp; lanjutkan</button>
  </form>
  <form method="post" action="<?= url('auth/resend-otp') ?>">
    <?= csrf_field() ?>
    <button class="btn" type="submit" style="background:#fff;color:var(--dark)!important;border:1px solid var(--line)">Kirim ulang kode</button>
  </form>
</section>
