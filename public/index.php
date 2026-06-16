<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Jalankan Session Selalu di Pintu Utama
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Membaca File .env (Membaca file di root folder satu tingkat di atas public/)
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Abaikan komentar
        
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim(trim($value), '"\''); // Bersihkan tanda kutip ganda/tunggal
            
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Muat file konfigurasi tambahan (SITE_NAME, formatRp(), dll) jika ada di folder luar
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
}

// 3. Atur Debug Mode berdasarkan status di .env (Default: false demi keamanan)
$appDebug = getenv('APP_DEBUG') ?: 'false';
if ($appDebug === 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// 4. Autoload Kelas Otomatis (Mencari otomatis ke folder core, controllers, dan models)
spl_autoload_register(function ($className) {
    // Jalur folder relatif terhadap file index.php ini
    $paths = [
        '/../app/core/',
        '/../app/controllers/',
        '/../app/models/'
    ];

    foreach ($paths as $path) {
        // Menggunakan realpath untuk mengubah ../ menjadi path absolut asli sistem operasi
        $file = realpath(__DIR__ . $path . $className . '.php');
        
        if ($file && file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 5. Jalankan Router Utama Aplikasi
if (class_exists('App')) {
    $app = new App();
} else {
    echo "<div style='font-family: sans-serif; padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px; max-width: 600px; margin: 40px auto; border: 1px solid #f5c6cb;'>";
    echo "<h3 style='margin-top:0;'>⚠️ Mini MVC Error:</h3>";
    echo "Sistem utama berhasil dimuat di folder <code>public/</code>, tetapi kelas inti <strong>App</strong> tidak ditemukan oleh Autoloader.<br><br>";
    echo "Pastikan file tersebut ada di: <code>app/core/App.php</code> dan penulisan nama kelasnya menggunakan case-sensitive yang benar (<strong>class App</strong>).";
    echo "</div>";
}