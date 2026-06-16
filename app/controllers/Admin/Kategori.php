<?php

class Kategori extends Controller {

    public function __construct() {
        if (!isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'auth/loginadmin');
            exit;
        }
    }

    public function index() {
        $data['pageTitle'] = 'Data Kategori';
        $data['kategori'] = $this->model('m_kategori')->getAll();
        
        $this->renderAdmin('kategori', $data);
    }

    public function tambah() {
        $data['pageTitle'] = 'Tambah Kategori';
        $this->renderAdmin('kategori-tambah', $data);
    }

    public function prosesTambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model('m_kategori')->tambahData($_POST) > 0) {
                Flasher::setFlash('success', 'Kategori berhasil ditambah!');
            } else {
                Flasher::setFlash('error', 'Gagal menambah kategori.');
            }
            header('Location: ' . BASEURL . 'kategori');
            exit;
        }
    }

    public function hapus($id) {
        if ($this->model('m_kategori')->hapusData($id) > 0) {
            Flasher::setFlash('success', 'Kategori berhasil dihapus!');
        } else {
            Flasher::setFlash('error', 'Gagal menghapus kategori.');
        }
        header('Location: ' . BASEURL . 'kategori');
        exit;
    }

    private function renderAdmin($view, $data = []) {
        $this->view('layouts/header-admin', $data);
        $this->view('layouts/sidebar-admin', $data);
        $this->view('admin/' . $view, $data);
        $this->view('layouts/footer-admin', $data);
    }
}