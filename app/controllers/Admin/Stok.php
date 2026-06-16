<?php

class Stok extends Controller {

    public function __construct() {
        if (!isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'auth/loginadmin');
            exit;
        }
    }

    // Menampilkan daftar stok produk
    public function index() {
        $data['pageTitle'] = 'Manajemen Stok';
        // Menggunakan model yang sama (m_produk) karena stok ada di tabel produk
        $data['produk'] = $this->model('m_produk')->getAll();
        
        $this->renderAdmin('stok', $data);
    }

    // Proses update stok (langsung dari tabel stok)
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model('m_produk')->updateStok($_POST) > 0) {
                Flasher::setFlash('success', 'Stok berhasil diperbarui!');
            } else {
                Flasher::setFlash('error', 'Gagal update stok.');
            }
            header('Location: ' . BASEURL . 'stok');
            exit;
        }
    }

    private function renderAdmin($view, $data = []) {
        $this->view('layouts/header-admin', $data);
        $this->view('layouts/sidebar-admin', $data);
        $this->view('admin/' . $view, $data);
        $this->view('layouts/footer-admin', $data);
    }
}