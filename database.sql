-- =============================================
-- DATABASE: haji_ayat_kurma
-- =============================================
CREATE DATABASE IF NOT EXISTS haji_ayat_kurma CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE haji_ayat_kurma;

-- TABEL ADMIN
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABEL PELANGGAN
CREATE TABLE IF NOT EXISTS pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengguna VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_penerima VARCHAR(100),
    no_hp VARCHAR(20),
    alamat TEXT,
    kode_pos VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABEL KATEGORI
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(10) DEFAULT '📦',
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABEL PRODUK
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT,
    nama VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(12,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    satuan VARCHAR(50) DEFAULT '500g',
    icon VARCHAR(10) DEFAULT '📦',
    badge VARCHAR(50) DEFAULT '',
    foto VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

-- TABEL PESANAN
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice VARCHAR(30) NOT NULL UNIQUE,
    pelanggan_id INT NOT NULL,
    nama_penerima VARCHAR(100),
    no_hp VARCHAR(20),
    alamat_kirim TEXT,
    kode_pos VARCHAR(10),
    metode_bayar ENUM('qris','transfer','cod') DEFAULT 'transfer',
    subtotal DECIMAL(12,2) DEFAULT 0,
    ongkir DECIMAL(12,2) DEFAULT 15000,
    diskon DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) DEFAULT 0,
    catatan TEXT,
    status ENUM('pending','processed','paid','cancelled') DEFAULT 'pending',
    bukti_bayar VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id)
);

-- TABEL DETAIL PESANAN
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    produk_id INT NOT NULL,
    nama_produk VARCHAR(150),
    satuan VARCHAR(50),
    harga DECIMAL(12,2),
    qty INT DEFAULT 1,
    subtotal DECIMAL(12,2),
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id),
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

-- TABEL API TOKENS (autentikasi REST API)
CREATE TABLE IF NOT EXISTS api_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expired_at TIMESTAMP NULL,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
);

-- TABEL API CART (keranjang untuk REST API, per pelanggan di DB)
CREATE TABLE IF NOT EXISTS api_cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id INT NOT NULL,
    produk_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    UNIQUE KEY uq_cart (pelanggan_id, produk_id),
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
);

-- =============================================
-- DATA AWAL
-- =============================================

-- Admin default: admin@ayatkurma.com / admin123
INSERT INTO admin (nama, email, password) VALUES
('Super Admin', 'admin@ayatkurma.com', '$2y$12$PBuBRS3c83FjeyNrs.8mvuvJR8yzH.9r0aL1oJ8/.LhcPcX2M4Jp6');
-- password: admin123 (bcrypt)

-- Pelanggan demo: pelanggan@email.com / 123456
INSERT INTO pelanggan (nama_pengguna, email, password, nama_penerima, no_hp, alamat, kode_pos) VALUES
('Siti Aminah', 'pelanggan@email.com', '$2y$12$RkEmNSiKJTin212UjS.zge7kgI4pZBNZD6p/3cAsF3bvsC9IJQAPe', 'Siti Aminah', '081234567890', 'Jl. Pahlawan No.12 RT 03/RW 02 Kel. Sungai Pinang Dalam, Samarinda', '75117');
-- password: 123456

-- Kategori
INSERT INTO kategori (nama_kategori, icon, deskripsi) VALUES
('Kurma Madinah', '🌴', 'Kurma pilihan langsung dari Madinah Al-Munawwarah'),
('Madu & Habbatussauda', '🍯', 'Madu murni dan produk habbatussauda berkualitas'),
('Tumbuhan Herbal', '🌿', 'Rempah dan tumbuhan herbal pilihan dari Timur Tengah'),
('Oleh-oleh Haji', '📿', 'Perlengkapan dan oleh-oleh ibadah haji & umroh');

-- Produk
INSERT INTO produk (kategori_id, nama, deskripsi, harga, stok, satuan, icon, badge) VALUES
(1, 'Kurma Ajwa Premium', 'Kurma Ajwa asli dari Madinah Al-Munawwarah, dipilih langsung dari pohon kurma pilihan. Dikenal sebagai kurma Rasulullah SAW yang memiliki manfaat luar biasa bagi kesehatan. Rasa manis alami dengan tekstur lembut dan biji kecil.', 125000, 50, '500g', '🌴', 'Best Seller'),
(1, 'Kurma Medjool Jumbo', 'Kurma Medjool berukuran jumbo dengan rasa manis alami dan tekstur lembut. Sangat cocok untuk sajian ifthar Ramadan dan hidangan spesial.', 89000, 35, '500g', '🟤', 'Favorit'),
(1, 'Kurma Sukkari Madinah', 'Kurma Sukkari terkenal dengan rasa manis legit dan tekstur kering yang khas. Langsung dari petani Madinah terpercaya.', 98000, 42, '500g', '🌿', 'Baru'),
(1, 'Kismis Arab Premium', 'Kismis Arab pilihan, manis alami tanpa bahan pengawet. Cocok untuk cemilan sehat keluarga.', 55000, 60, '250g', '🍇', ''),
(2, 'Madu Habbatussauda Asli', 'Madu murni dengan campuran habbatussauda berkualitas tinggi. Kaya antioksidan dan manfaat kesehatan alami untuk daya tahan tubuh.', 145000, 28, '500ml', '🍯', 'Organik'),
(3, 'Saffron Iran Original', 'Saffron asli Iran berkualitas tinggi, aroma harum dan warna intens. Bermanfaat untuk kesehatan dan masakan premium.', 210000, 15, '2g', '🌸', 'Premium'),
(4, 'Air Zamzam Kemasan', 'Air Zamzam asli dari Mekah Al-Mukarramah, dikemas dengan standar kesehatan internasional. Tersedia dalam berbagai ukuran.', 35000, 100, '1L', '💧', ''),
(4, 'Tasbih Kayu Cendana', 'Tasbih kayu cendana asli dengan harum khas. Dibuat dengan pengerjaan tangan oleh pengrajin terpilih dari 33 dan 99 butir.', 75000, 30, 'pcs', '📿', '');

-- Pesanan demo
INSERT INTO pesanan (invoice, pelanggan_id, nama_penerima, no_hp, alamat_kirim, kode_pos, metode_bayar, subtotal, ongkir, diskon, total, status) VALUES
('INV-2026-0001', 1, 'Siti Aminah', '081234567890', 'Jl. Pahlawan No.12, Samarinda', '75117', 'transfer', 250000, 0, 0, 250000, 'paid'),
('INV-2026-0002', 1, 'Siti Aminah', '081234567890', 'Jl. Pahlawan No.12, Samarinda', '75117', 'qris', 145000, 15000, 0, 160000, 'processed'),
('INV-2026-0003', 1, 'Siti Aminah', '081234567890', 'Jl. Pahlawan No.12, Samarinda', '75117', 'cod', 105000, 15000, 0, 120000, 'pending');

INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, satuan, harga, qty, subtotal) VALUES
(1, 1, 'Kurma Ajwa Premium', '500g', 125000, 2, 250000),
(2, 5, 'Madu Habbatussauda Asli', '500ml', 145000, 1, 145000),
(3, 1, 'Kurma Ajwa Premium', '500g', 125000, 1, 125000),
(3, 2, 'Kurma Medjool Jumbo', '500g', 89000, 1, 89000);
