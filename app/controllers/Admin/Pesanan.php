<?php

class Pesanan extends Controller {

    public function __construct() {
        if (!isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'auth/loginadmin');
            exit;
        }
    }

    // Menampilkan daftar semua pesanan
    public function index() {
        $data['pageTitle'] = 'Manajemen Pesanan';
        $data['pesanan'] = $this->model('m_pesanan')->getAll();
        
        $this->renderAdmin('pesanan', $data);
    }

    // Menampilkan detail pesanan spesifik
    public function detail($id) {
        $data['pageTitle'] = 'Detail Pesanan';
        $data['pesanan'] = $this->model('m_pesanan')->getById($id);
        $data['item'] = $this->model('m_pesanan')->getItemByPesananId($id);
        
        $this->renderAdmin('pesanan-detail', $data);
    }

    // Mengubah status pesanan (Misal: dari 'Menunggu' ke 'Dikirim')
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model('m_pesanan')->updateStatus($_POST) > 0) {
                Flasher::setFlash('success', 'Status pesanan berhasil diupdate!');
            } else {
                Flasher::setFlash('error', 'Gagal update status.');
            }
            header('Location: ' . BASEURL . 'pesanan/detail/' . $_POST['id']);
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