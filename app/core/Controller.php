<?php

class Controller {
    // Fungsi memanggil file views murni dengan membawa data terstruktur
    public function view($view, $data = []) {
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die("View '$view' tidak ditemukan.");
        }
    }

    // Fungsi instansiasi kelas model ber-awalan m_
    public function model($model) {
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            return new $model();
        }
        die("Model '$model' tidak ditemukan.");
    }
}