<section class="card">
  <h1>Masuk &amp; nikmati tarif bersih</h1>
  <p class="sub">Masukkan nomor HP, kami kirim kode OTP 4 digit.</p>
  <form method="post" action="<?= url('auth/login') ?>">
    <?= csrf_field() ?>
    <label for="no_hp">Nomor HP (WhatsApp)</label>
    <input id="no_hp" name="no_hp" inputmode="tel" placeholder="0812 3456 7890" required value="<?= e($old['no_hp'] ?? '') ?>">
    <button class="btn" type="submit">Lanjutkan &amp; kirim OTP</button>
  </form>
  <p class="sub" style="margin-top:16px">Belum punya akun? <a class="link" href="<?= url('auth/register') ?>">Daftar</a></p>
</section>
