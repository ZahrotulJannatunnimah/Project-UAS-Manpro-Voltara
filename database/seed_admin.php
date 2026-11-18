<?php
// Jalankan sekali dari terminal: php database/seed_admin.php
// Membuat akun admin dengan password ter-hash (password_hash).
$c   = require __DIR__ . '/../config/database.php';
$pdo = new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4", $c['user'], $c['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$password = 'admin123'; // ganti setelah login pertama
$st = $pdo->prepare("INSERT INTO users (nama, email, no_hp, password_hash, role, is_verified)
                     VALUES ('Admin VOLTARA', 'admin@voltara.test', '6280000000000', :h, 'admin', 1)
                     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)");
$st->execute(['h' => password_hash($password, PASSWORD_DEFAULT)]);
echo "Admin siap. Email: admin@voltara.test | Password: $password\n";
