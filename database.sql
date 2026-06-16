CREATE DATABASE IF NOT EXISTS db_minimarket;
USE db_minimarket;

CREATE TABLE IF NOT EXISTS user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Operator') NOT NULL
);

CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    nama_barang VARCHAR(150) NOT NULL UNIQUE,
    harga DECIMAL(12,2) NOT NULL,
    stok INT NOT NULL,
    foto_barang VARCHAR(100) DEFAULT 'default.jpg',
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS member (
    id_member INT AUTO_INCREMENT PRIMARY KEY,
    nama_member VARCHAR(100) NOT NULL,
    kode_member VARCHAR(50) NOT NULL UNIQUE,
    diskon_persen DECIMAL(5,2) DEFAULT 0.00
);

CREATE TABLE IF NOT EXISTS transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_member INT DEFAULT NULL,
    tanggal DATETIME NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    diskon DECIMAL(12,2) NOT NULL,
    pajak DECIMAL(12,2) NOT NULL,
    total_bayar DECIMAL(12,2) NOT NULL,
    kembalian DECIMAL(12,2) NOT NULL,
    uang_diterima DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_member) REFERENCES member(id_member) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS detail_transaksi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    id_barang INT NOT NULL,
    jumlah_beli INT NOT NULL,
    harga_satuan DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang) ON DELETE CASCADE
);

INSERT INTO user (id_user, username, password, role) VALUES 
(1, 'admin', '$2y$10$wM4c1p9e8l.1w69L6E33ZOnc19Fv1fWp0s/W3i3q3R0zB5s/W3i3q', 'Admin'),
(2, 'operator', '$2y$10$eS3q3R0zB5s/W3i3q3R0zOnc19Fv1fWp0s/W3i3q3R0zB5s/W3i3q', 'Operator')
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO kategori (id_kategori, nama_kategori) VALUES 
(1, 'Makanan'),
(2, 'Minuman'),
(3, 'Peralatan Mandi')
ON DUPLICATE KEY UPDATE nama_kategori=nama_kategori;

INSERT INTO barang (id_barang, id_kategori, nama_barang, harga, stok, foto_barang) VALUES 
(1, 1, 'Mie Instan Goreng', 3500.00, 100, 'default.jpg'),
(2, 2, 'Air Mineral 600ml', 4000.00, 150, 'default.jpg'),
(3, 3, 'Sabun Mandi Cair', 18000.00, 45, 'default.jpg')
ON DUPLICATE KEY UPDATE nama_barang=nama_barang;

INSERT INTO member (id_member, nama_member, kode_member, diskon_persen) VALUES 
(1, 'Budi Santoso', 'MBR001', 5.00),
(2, 'Siti Aminah', 'MBR002', 10.00)
ON DUPLICATE KEY UPDATE kode_member=kode_member;
