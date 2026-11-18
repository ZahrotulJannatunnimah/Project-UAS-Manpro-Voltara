<section class="card">
  <h1>Kenalan dulu, yuk!</h1>
  <p class="sub">Daftar dengan nomor HP, kode OTP dikirim untuk verifikasi.</p>
  <form method="post" action="<?= url('auth/register') ?>">
    <?= csrf_field() ?>
    <label for="nama">Nama lengkap</label>
    <input id="nama" name="nama" maxlength="100" required value="<?= e($old['nama'] ?? '') ?>">
    <label for="no_hp">Nomor HP (WhatsApp)</label>
    <input id="no_hp" name="no_hp" inputmode="tel" placeholder="0812 3456 7890" required value="<?= e($old['no_hp'] ?? '') ?>">
    <label for="role">Daftar sebagai</label>
    <select id="role" name="role">
      <option value="penumpang">Penumpang</option>
      <option value="driver" <?= ($old['role'] ?? '') === 'driver' ? 'selected' : '' ?>>Mitra Driver</option>
    </select>
    <button class="btn" type="submit">Kirim kode OTP</button>
  </form>
  <p class="sub" style="margin-top:16px">Sudah punya akun? <a class="link" href="<?= url('auth/login') ?>">Masuk</a></p>
</section>
