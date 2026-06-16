<?php

class App {
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseURL();

        // 1. Definisikan Controller Default
        // Jika URL kosong, arahkan ke Home
        $controllerName = isset($url[0]) ? ucfirst($url[0]) : 'Home';
        
        // 2. Tentukan apakah ini akses Admin
        $is_admin = (isset($url[0]) && strtolower($url[0]) === 'admin');
        
        if ($is_admin) {
            array_shift($url); // Hapus 'admin'
            $controllerName = isset($url[0]) ? ucfirst($url[0]) : 'Dashboard';
            $file = APP_PATH . '/controllers/Admin/' . $controllerName . '.php';
        } else {
            $file = APP_PATH . '/controllers/' . $controllerName . '.php';
        }

        // 3. Load Controller
        if (file_exists($file)) {
            require_once $file;
            $this->controller = $controllerName;
        } else {
            // Jika file tidak ada, fallback ke Home
            require_once APP_PATH . '/controllers/Home.php';
            $this->controller = 'Home';
        }

        $this->controller = new $this->controller;

        // 4. Deteksi Method
        if (isset($url[0])) {
            if (method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                array_shift($url);
            }
        }

        // 5. Deteksi Params
        $this->params = $url ? array_values($url) : [];

        // 6. Jalankan Controller & Method
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