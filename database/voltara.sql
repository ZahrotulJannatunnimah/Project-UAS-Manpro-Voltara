-- VOLTARA | Skema database minggu 3
-- Import lewat phpMyAdmin (tab Import) atau: mysql -u root < database/voltara.sql

CREATE DATABASE IF NOT EXISTS voltara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voltara;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ulasan, detail_pesanan, penawaran, pesanan, driver_dokumen, otp_codes, drivers, users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama          VARCHAR(100) NOT NULL,
  email         VARCHAR(120) NULL,
  no_hp         VARCHAR(20)  NOT NULL,
  password_hash VARCHAR(255) NULL COMMENT 'hanya untuk admin, penumpang/driver masuk via OTP',
  role          ENUM('penumpang','driver','admin') NOT NULL DEFAULT 'penumpang',
  foto          VARCHAR(255) NULL,
  is_verified   TINYINT(1) NOT NULL DEFAULT 0,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_no_hp (no_hp),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE drivers (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id           INT UNSIGNED NOT NULL,
  merek_tipe        VARCHAR(80)  NOT NULL,
  plat_nomor        VARCHAR(15)  NOT NULL,
  kapasitas_baterai SMALLINT UNSIGNED NOT NULL COMMENT 'kWh',
  persen_baterai    TINYINT UNSIGNED NOT NULL DEFAULT 100,
  status_verifikasi ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  status_online     ENUM('offline','online','sibuk') NOT NULL DEFAULT 'offline',
  lat               DECIMAL(10,7) NULL,
  lng               DECIMAL(10,7) NULL,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_drivers_user (user_id),
  UNIQUE KEY uq_drivers_plat (plat_nomor),
  CONSTRAINT fk_drivers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE driver_dokumen (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  driver_id     INT UNSIGNED NOT NULL,
  jenis         ENUM('ktp','sim_a','stnk','foto_kendaraan') NOT NULL,
  nomor_dokumen VARCHAR(50) NULL,
  file_path     VARCHAR(255) NOT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_dokumen_driver (driver_id),
  CONSTRAINT fk_dokumen_driver FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE otp_codes (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT UNSIGNED NOT NULL,
  kode_hash  VARCHAR(255) NOT NULL,
  attempts   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  expired_at DATETIME NOT NULL,
  used_at    DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_otp_user (user_id),
  CONSTRAINT fk_otp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pesanan (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  penumpang_id   INT UNSIGNED NOT NULL,
  driver_id      INT UNSIGNED NULL COMMENT 'terisi setelah deal',
  alamat_jemput  VARCHAR(255) NOT NULL,
  jemput_lat     DECIMAL(10,7) NOT NULL,
  jemput_lng     DECIMAL(10,7) NOT NULL,
  alamat_tujuan  VARCHAR(255) NOT NULL,
  tujuan_lat     DECIMAL(10,7) NOT NULL,
  tujuan_lng     DECIMAL(10,7) NOT NULL,
  jarak_km       DECIMAL(6,2) NOT NULL,
  tarif_min      INT UNSIGNED NOT NULL,
  tarif_max      INT UNSIGNED NOT NULL,
  harga_awal     INT UNSIGNED NOT NULL,
  harga_sepakat  INT UNSIGNED NULL,
  status         ENUM('mencari','negosiasi','deal','menuju_titik','perjalanan','selesai','batal') NOT NULL DEFAULT 'mencari',
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pesanan_status (status),
  KEY idx_pesanan_penumpang (penumpang_id),
  KEY idx_pesanan_driver (driver_id),
  CONSTRAINT fk_pesanan_penumpang FOREIGN KEY (penumpang_id) REFERENCES users(id),
  CONSTRAINT fk_pesanan_driver FOREIGN KEY (driver_id) REFERENCES drivers(id)
) ENGINE=InnoDB;

CREATE TABLE penawaran (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pesanan_id      INT UNSIGNED NOT NULL,
  driver_id       INT UNSIGNED NOT NULL,
  harga_tawar     INT UNSIGNED NOT NULL,
  jarak_jemput_km DECIMAL(6,2) NOT NULL,
  status          ENUM('menunggu','dipilih','ditolak','kedaluwarsa') NOT NULL DEFAULT 'menunggu',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_penawaran (pesanan_id, driver_id),
  CONSTRAINT fk_penawaran_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  CONSTRAINT fk_penawaran_driver FOREIGN KEY (driver_id) REFERENCES drivers(id)
) ENGINE=InnoDB;

CREATE TABLE detail_pesanan (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pesanan_id    INT UNSIGNED NOT NULL,
  waktu_jemput  DATETIME NULL,
  waktu_selesai DATETIME NULL,
  total_bayar   INT UNSIGNED NOT NULL,
  metode_bayar  ENUM('tunai') NOT NULL DEFAULT 'tunai',
  catatan       VARCHAR(255) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_detail_pesanan (pesanan_id),
  CONSTRAINT fk_detail_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ulasan (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pesanan_id   INT UNSIGNED NOT NULL,
  penumpang_id INT UNSIGNED NOT NULL,
  driver_id    INT UNSIGNED NOT NULL,
  rating       TINYINT UNSIGNED NOT NULL,
  komentar     VARCHAR(255) NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ulasan_pesanan (pesanan_id),
  CONSTRAINT chk_ulasan_rating CHECK (rating BETWEEN 1 AND 5),
  CONSTRAINT fk_ulasan_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  CONSTRAINT fk_ulasan_penumpang FOREIGN KEY (penumpang_id) REFERENCES users(id),
  CONSTRAINT fk_ulasan_driver FOREIGN KEY (driver_id) REFERENCES drivers(id)
) ENGINE=InnoDB;

-- ===== Data contoh (fiktif, untuk uji dan screenshot) =====
INSERT INTO users (id, nama, no_hp, role, is_verified) VALUES
 (1, 'Dimas Pratama',   '6281200000001', 'penumpang', 1),
 (2, 'Hendra Gunawan',  '6281200000002', 'driver',    1);

INSERT INTO drivers (id, user_id, merek_tipe, plat_nomor, kapasitas_baterai, persen_baterai, status_verifikasi, status_online, lat, lng) VALUES
 (1, 2, 'Contoh EV Roda 4', 'L 1234 AB', 60, 82, 'disetujui', 'online', -7.3100000, 112.7700000);

INSERT INTO pesanan (id, penumpang_id, driver_id, alamat_jemput, jemput_lat, jemput_lng, alamat_tujuan, tujuan_lat, tujuan_lng,
                     jarak_km, tarif_min, tarif_max, harga_awal, harga_sepakat, status) VALUES
 (1, 1, 1, 'Titik jemput contoh', -7.2900000, 112.7600000, 'UPN Veteran Jawa Timur', -7.3335000, 112.7886000,
  8.50, 30000, 45000, 35000, 38000, 'selesai');

INSERT INTO penawaran (pesanan_id, driver_id, harga_tawar, jarak_jemput_km, status) VALUES
 (1, 1, 38000, 1.20, 'dipilih');

INSERT INTO detail_pesanan (pesanan_id, waktu_jemput, waktu_selesai, total_bayar) VALUES
 (1, '2026-11-18 09:05:00', '2026-11-18 09:30:00', 38000);

INSERT INTO ulasan (pesanan_id, penumpang_id, driver_id, rating, komentar) VALUES
 (1, 1, 1, 5, 'Ramah dan tepat waktu');
