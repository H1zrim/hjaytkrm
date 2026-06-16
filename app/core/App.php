<?php

class App {
    protected $controller = 'Home';
    protected $method     = 'index';
    protected $params     = [];

    public function __construct() {
        $url = $this->parseURL();

        // 1. Tentukan apakah request untuk admin
        $is_admin = (isset($url[0]) && strtolower($url[0]) === 'admin');

        if ($is_admin) {
            array_shift($url);  // Hapus segmen 'admin'
            $controllerName = !empty($url) ? ucfirst($url[0]) : 'Dashboard';
            array_shift($url);  // Hapus segmen nama controller
            $file = APP_PATH . '/controllers/Admin/' . $controllerName . '.php';
        } else {
            $controllerName = !empty($url) ? ucfirst($url[0]) : 'Home';
            array_shift($url);  // Hapus segmen nama controller
            $file = APP_PATH . '/controllers/' . $controllerName . '.php';
        }

        // 2. Load Controller
        if (file_exists($file)) {
            require_once $file;
        } else {
            // Fallback ke Home jika controller tidak ditemukan
            require_once APP_PATH . '/controllers/Home.php';
            $controllerName = 'Home';
        }

        $this->controller = new $controllerName();

        // 3. Deteksi Method dari segmen URL berikutnya
        if (!empty($url) && method_exists($this->controller, $url[0])) {
            $this->method = $url[0];
            array_shift($url);
        }

        // 4. Sisa segmen jadi parameter
        $this->params = array_values($url);

        // 5. Jalankan
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            return explode('/', filter_var($url, FILTER_SANITIZE_URL));
        }
        return [];
    }
}
