<?php
// 1. Error Reporting & Session
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Load .env (Jika ada)
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            putenv(sprintf('%s=%s', trim($name), trim(trim($value), '"\'')));
        }
    }
}

// 3. Load Config
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
}

// 4. Perbaikan Autoloader (Mendukung sub-folder /Admin/)
spl_autoload_register(function ($className) {
    $baseDirs = [
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/'
    ];

    foreach ($baseDirs as $dir) {
        // Cek folder utama dan subfolder (seperti Admin/)
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }

        // Cari rekursif di dalam sub-folder (Contoh: Admin/Pelanggan.php)
        $subDirs = glob($dir . '*/', GLOB_ONLYDIR);
        foreach ($subDirs as $subDir) {
            $file = $subDir . $className . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// 5. Jalankan Router
if (class_exists('App')) {
    new App();
} else {
    die("Sistem gagal memuat kelas App. Pastikan app/core/App.php ada.");
}