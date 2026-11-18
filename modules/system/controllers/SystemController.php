<?php
class SystemController
{
    // Halaman bukti "web terhubung ke database" untuk screenshot minggu 3.
    public function koneksi(): void
    {
        if (!APP_DEBUG) {
            Router::notFound();
            return;
        }
        $pdo    = Database::conn();
        $versi  = $pdo->query('SELECT VERSION()')->fetchColumn();
        $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
        $tabel  = [];
        foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $t) {
            $tabel[$t] = (int) $pdo->query('SELECT COUNT(*) FROM `' . str_replace('`', '', $t) . '`')->fetchColumn();
        }
        view('system', 'koneksi', ['judul' => 'Cek Koneksi', 'versi' => $versi, 'dbName' => $dbName, 'tabel' => $tabel]);
    }
}
