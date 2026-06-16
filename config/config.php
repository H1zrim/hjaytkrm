<?php

// 1. Path aplikasi (digunakan oleh App.php router)
define('APP_PATH', dirname(__DIR__) . '/app');

// 2. BASEURL otomatis — strip "/public" agar URL bersih di XAMPP maupun InfinityFree
$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$scriptDir = preg_replace('#/public$#', '', $scriptDir); // hapus /public
define('BASEURL', "$protocol://$host$scriptDir/");

// 3. Database — baca dari .env;
define('DB_HOST', getenv('DB_HOST') ?: 'sql211.infinityfree.com');
define('DB_USER', getenv('DB_USERNAME') ?: 'if0_41691367');
define('DB_PASS', getenv('DB_PASSWORD') ?: 'hizbullah122305');
define('DB_NAME', getenv('DB_DATABASE') ?: 'if0_41691367_haji_ayat_kurma');

// 4. Info Aplikasi
define('SITE_NAME',    getenv('APP_NAME') ?: 'Haji Ayat Kurma');
define('SITE_TAGLINE', 'Produk Haji & Kurma Pilihan Terbaik');

// 5. Helper koneksi PDO (singleton)
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:30px;text-align:center;">
                <h3>&#9888;&#65039; Koneksi Database Gagal</h3>
                <p>Pastikan MySQL XAMPP sudah aktif dan database <strong>' . DB_NAME . '</strong> sudah dibuat.</p>
                <small style="color:#999">Error: ' . htmlspecialchars($e->getMessage()) . '</small>
            </div>');
        }
    }
    return $pdo;
}
