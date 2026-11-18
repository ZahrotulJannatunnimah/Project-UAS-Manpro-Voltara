<?php
class Otp
{
    // Buat kode 4 digit baru, kode lama yang belum terpakai dihanguskan.
    public static function issue(int $userId): string
    {
        $pdo = Database::conn();
        $pdo->prepare('UPDATE otp_codes SET used_at = :now WHERE user_id = :u AND used_at IS NULL')
            ->execute(['now' => date('Y-m-d H:i:s'), 'u' => $userId]);

        $kode = (string) random_int(1000, 9999);
        $pdo->prepare('INSERT INTO otp_codes (user_id, kode_hash, expired_at) VALUES (:u, :h, :exp)')
            ->execute([
                'u'   => $userId,
                'h'   => password_hash($kode, PASSWORD_DEFAULT),
                'exp' => date('Y-m-d H:i:s', time() + (int) app_config('otp_ttl')),
            ]);
        return $kode;
    }

    // Return [berhasil(bool), pesan(string)]
    public static function verify(int $userId, string $kode): array
    {
        $pdo = Database::conn();
        $st  = $pdo->prepare('SELECT * FROM otp_codes WHERE user_id = :u AND used_at IS NULL ORDER BY id DESC LIMIT 1');
        $st->execute(['u' => $userId]);
        $row = $st->fetch();

        if (!$row) {
            return [false, 'Kode tidak ditemukan. Minta kode baru.'];
        }
        if (strtotime($row['expired_at']) < time()) {
            return [false, 'Kode sudah kedaluwarsa. Minta kode baru.'];
        }
        if ((int) $row['attempts'] >= (int) app_config('otp_max_attempt')) {
            return [false, 'Terlalu banyak percobaan. Minta kode baru.'];
        }
        if (!password_verify($kode, $row['kode_hash'])) {
            $pdo->prepare('UPDATE otp_codes SET attempts = attempts + 1 WHERE id = :id')->execute(['id' => $row['id']]);
            return [false, 'Kode salah, coba lagi.'];
        }

        $pdo->prepare('UPDATE otp_codes SET used_at = :now WHERE id = :id')
            ->execute(['now' => date('Y-m-d H:i:s'), 'id' => $row['id']]);
        return [true, 'OK'];
    }
}
