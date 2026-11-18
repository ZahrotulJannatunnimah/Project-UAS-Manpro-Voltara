<?php
class User
{
    public static function findByPhone(string $hp): ?array
    {
        $st = Database::conn()->prepare('SELECT * FROM users WHERE no_hp = :hp LIMIT 1');
        $st->execute(['hp' => $hp]);
        return $st->fetch() ?: null;
    }

    public static function find(int $id): ?array
    {
        $st = Database::conn()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        return $st->fetch() ?: null;
    }

    public static function create(string $nama, string $hp, string $role): int
    {
        $pdo = Database::conn();
        $st  = $pdo->prepare('INSERT INTO users (nama, no_hp, role) VALUES (:nama, :hp, :role)');
        $st->execute(['nama' => $nama, 'hp' => $hp, 'role' => $role]);
        return (int) $pdo->lastInsertId();
    }

    public static function markVerified(int $id): void
    {
        $st = Database::conn()->prepare('UPDATE users SET is_verified = 1 WHERE id = :id');
        $st->execute(['id' => $id]);
    }
}
