<?php
class Controller {
    public function view($view, $data = []) {
        // Path absolut dari root project
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            extract($data);
            require_once $viewPath;
        } else {
            die("View tidak ditemukan: " . $viewPath);
        }
    }

    public function model($model) {
        $modelPath = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        die("Model tidak ditemukan: " . $modelPath);
    }
}