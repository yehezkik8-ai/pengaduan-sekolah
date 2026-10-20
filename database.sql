CREATE DATABASE IF NOT EXISTS pengaduan_sekolah
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pengaduan_sekolah;

CREATE TABLE IF NOT EXISTS users (
  id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('murid','admin_it','petugas') NOT NULL DEFAULT 'murid',
  dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kategori (
  id_kategori INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pengaduan (
  id_pengaduan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_user INT UNSIGNED NOT NULL,
  id_kategori INT UNSIGNED NOT NULL,
  judul VARCHAR(150) NOT NULL,
  lokasi VARCHAR(150) NOT NULL,
  deskripsi TEXT NOT NULL,
  status ENUM('Menunggu','Diproses','Selesai') NOT NULL DEFAULT 'Menunggu',
  tanggal_laporan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pengaduan_user FOREIGN KEY (id_user) REFERENCES users(id_user),
  CONSTRAINT fk_pengaduan_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori),
  INDEX idx_pengaduan_user (id_user),
  INDEX idx_pengaduan_status (status),
  INDEX idx_pengaduan_tanggal (tanggal_laporan)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS riwayat_status (
  id_riwayat INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pengaduan INT UNSIGNED NOT NULL,
  id_user INT UNSIGNED NOT NULL,
  status ENUM('Menunggu','Diproses','Selesai') NOT NULL,
  catatan TEXT NULL,
  tanggal_perubahan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_riwayat_pengaduan FOREIGN KEY (id_pengaduan) REFERENCES pengaduan(id_pengaduan),
  CONSTRAINT fk_riwayat_user FOREIGN KEY (id_user) REFERENCES users(id_user),
  INDEX idx_riwayat_pengaduan (id_pengaduan)
) ENGINE=InnoDB;

INSERT IGNORE INTO kategori (nama_kategori) VALUES
('Meja dan Kursi'), ('Toilet'), ('Lampu dan Listrik'),
('Ruang Kelas'), ('Perpustakaan'), ('Fasilitas Lainnya');
