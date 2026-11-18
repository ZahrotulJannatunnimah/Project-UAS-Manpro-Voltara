# VOLTARA (PHP Native, Modular MVC)

1. Taruh folder ini di `htdocs/voltara` (XAMPP). Nyalakan Apache dan MySQL.
2. Import `database/voltara.sql` lewat phpMyAdmin.
3. Cek `config/database.php` (default XAMPP: user root, password kosong).
4. Buka http://localhost/voltara/public/index.php
5. Bukti koneksi DB: http://localhost/voltara/public/index.php?r=system/koneksi
6. Opsional, buat admin: `php database/seed_admin.php`

Rute memakai format `?r=modul/aksi`. Controller ada di `modules/<modul>/controllers`.
Salin `config/database.example.php` menjadi `config/database.php` di komputer masing-masing (file itu tidak masuk Git).
