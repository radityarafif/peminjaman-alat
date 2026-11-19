CREATE DATABASE IF NOT EXISTS peminjamann_alat;
USE db_peminjaman_alat;

CREATE TABLE users(
 id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
 role ENUM('admin','petugas','peminjam') NOT NULL);
CREATE TABLE kategori(
 id INT AUTO_INCREMENT PRIMARY KEY, nama_kategori VARCHAR(100) NOT NULL);
CREATE TABLE alat(
 id INT AUTO_INCREMENT PRIMARY KEY, nama_alat VARCHAR(100) NOT NULL,
 kategori_id INT NULL, stok INT NOT NULL DEFAULT 0,
 kondisi ENUM('baik','rusak ringan','rusak berat') DEFAULT 'baik',
 FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL);
CREATE TABLE peminjaman(
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, alat_id INT NOT NULL,
 jumlah INT NOT NULL, tgl_pinjam DATE NOT NULL, tgl_kembali_rencana DATE NOT NULL,
 status ENUM('menunggu','disetujui','ditolak','dikembalikan') DEFAULT 'menunggu',
 petugas_id INT NULL,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (alat_id) REFERENCES alat(id) ON DELETE RESTRICT);
CREATE TABLE pengembalian(
 id INT AUTO_INCREMENT PRIMARY KEY, peminjaman_id INT NOT NULL UNIQUE,
 tgl_kembali DATE NOT NULL, kondisi ENUM('baik','rusak ringan','rusak berat') DEFAULT 'baik',
 denda INT DEFAULT 0, catatan TEXT,
 FOREIGN KEY (peminjaman_id) REFERENCES peminjaman(id) ON DELETE CASCADE);
CREATE TABLE log_aktivitas(
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NULL, aktivitas VARCHAR(255) NOT NULL,
 waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL);

-- Password semua akun contoh: password
INSERT INTO users(nama,username,password,role) VALUES
('Administrator','admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('Petugas Satu','petugas','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','petugas'),
('Peminjam Satu','peminjam','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','peminjam');
INSERT INTO kategori(nama_kategori) VALUES ('Elektronik'),('Perkakas'),('Olahraga');
INSERT INTO alat(nama_alat,kategori_id,stok,kondisi) VALUES
('Laptop',1,5,'baik'),('Proyektor',1,3,'baik'),('Bor Listrik',2,4,'baik'),('Bola Basket',3,6,'baik');
