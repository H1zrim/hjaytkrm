<?php
// config/config.php

// Memuat data dari file .env secara aman jika file tersedia
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Definisikan Konstanta Global Aplikasi
define('BASEURL', $_ENV['BASEURL'] ?? 'http://localhost/haji_ayat_kurma/');
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'haji_ayat_kurma');

define('SITE_NAME', 'Haji Ayat Kurma');
define('SITE_TAGLINE', 'Produk Haji & Kurma Pilihan Terbaik');

// Helper Database Global PDO (Melanjutkan getDB dari rancangan Anda)
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:40px;color:#c0392b;background:#fff5f5;border:1px solid #fcc;border-radius:8px;margin:40px;text-align:center;">
                <h3>⚠️ Koneksi Database Gagal</h3>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
            </div>');
        }
    }
    return $pdo;
}