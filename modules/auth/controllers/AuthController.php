<?php
require_once BASE_PATH . '/modules/auth/models/User.php';
require_once BASE_PATH . '/modules/auth/models/Otp.php';

class AuthController
{
    public function register(): void
    {
        if (!is_post()) {
            view('auth', 'register', ['judul' => 'Daftar']);
            return;
        }
        csrf_verify();

        $nama = trim($_POST['nama'] ?? '');
        $hp   = normalize_phone($_POST['no_hp'] ?? '');
        $role = $_POST['role'] ?? 'penumpang';

        $err = [];
        if ($nama === '' || mb_strlen($nama) > 100) {
            $err[] = 'Nama wajib diisi (maksimal 100 karakter).';
        }
        if ($hp === null) {
            $err[] = 'Nomor HP tidak valid.';
        }
        if (!in_array($role, ['penumpang', 'driver'], true)) {
            $err[] = 'Peran tidak valid.';
        }
        if (!$err && User::findByPhone($hp)) {
            $err[] = 'Nomor HP sudah terdaftar, silakan masuk.';
        }
        if ($err) {
            flash_set('error', implode(' ', $err));
            view('auth', 'register', ['judul' => 'Daftar', 'old' => $_POST]);
            return;
        }

        $id = User::create($nama, $hp, $role);
        $this->kirimOtp($id);
    }

    public function login(): void
    {
        if (!is_post()) {
            view('auth', 'login', ['judul' => 'Masuk']);
            return;
        }
        csrf_verify();

        $hp   = normalize_phone($_POST['no_hp'] ?? '');
        $user = $hp ? User::findByPhone($hp) : null;
        if (!$user) {
            flash_set('error', 'Nomor HP belum terdaftar atau tidak valid.');
            view('auth', 'login', ['judul' => 'Masuk', 'old' => $_POST]);
            return;
        }
        $this->kirimOtp((int) $user['id']);
    }

    public function otp(): void
    {
        if (empty($_SESSION['otp_user_id'])) {
            redirect('auth/login');
        }
        view('auth', 'otp', ['judul' => 'Verifikasi OTP']);
    }

    public function verifyOtp(): void
    {
        $uid = (int) ($_SESSION['otp_user_id'] ?? 0);
        if (!is_post() || !$uid) {
            redirect('auth/login');
        }
        csrf_verify();

        $kode = preg_replace('/\D+/', '', $_POST['kode'] ?? '');
        [$ok, $pesan] = Otp::verify($uid, $kode);
        if (!$ok) {
            flash_set('error', $pesan);
            redirect('auth/otp');
        }

        $user = User::find($uid);
        User::markVerified($uid);
        session_regenerate_id(true);
        unset($_SESSION['otp_user_id']);
        $_SESSION['user'] = ['id' => (int) $user['id'], 'nama' => $user['nama'], 'role' => $user['role']];

        flash_set('success', 'Berhasil masuk. Selamat datang, ' . $user['nama'] . '!');
        redirect('home/dashboard');
    }

    public function resendOtp(): void
    {
        $uid = (int) ($_SESSION['otp_user_id'] ?? 0);
        if (!is_post() || !$uid) {
            redirect('auth/login');
        }
        csrf_verify();
        $this->kirimOtp($uid);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        flash_set('info', 'Kamu sudah keluar.');
        redirect('auth/login');
    }

    private function kirimOtp(int $userId): void
    {
        $_SESSION['otp_user_id'] = $userId;
        $kode = Otp::issue($userId);
        // TODO produksi: kirim $kode lewat gateway SMS/WhatsApp.
        if (APP_DEBUG) {
            flash_set('info', "Mode dev: kode OTP kamu $kode");
        }
        redirect('auth/otp');
    }
}
