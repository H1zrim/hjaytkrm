<?php

class Produk extends Controller {

    public function __construct() {
        // Proteksi: Pastikan admin sudah login
        if (!isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'auth/loginadmin');
            exit;
        }
    }

    // Menampilkan daftar produk
    public function index() {
        $data['pageTitle'] = 'Data Produk';
        // Memanggil model m_produk (pastikan file app/models/m_produk.php ada)
        $data['produk'] = $this->model('m_produk')->getAll();
        
        $this->renderAdmin('produk', $data);
    }

    // Menampilkan halaman tambah produk
    public function tambah() {
        $data['kategori'] = $this->model('m_kategori')->getAll();
        $data['pageTitle'] = 'Tambah Produk';
        $this->renderAdmin('produk-tambah', $data);
    }

    // Proses simpan data produk baru
    public function prosesTambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model('m_produk')->tambahData($_POST) > 0) {
                Flasher::setFlash('success', 'Produk berhasil ditambahkan!');
                header('Location: ' . BASEURL . 'produk');
                exit;
            } else {
                Flasher::setFlash('error', 'Gagal menambahkan produk.');
                header('Location: ' . BASEURL . 'produk/tambah');
                exit;
            }
        }
    }

    // Menampilkan halaman edit produk
    public function edit($id) {
        $data['kategori'] = $this->model('m_kategori')->getAll();
        $data['pageTitle'] = 'Edit Produk';
        $data['produk'] = $this->model('m_produk')->getById($id);
        $this->renderAdmin('produk-edit', $data);
    }

    // Proses update produk
    public function prosesEdit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model('m_produk')->updateData($_POST) > 0) {
                Flasher::setFlash('success', 'Produk berhasil diupdate!');
            } else {
                Flasher::setFlash('error', 'Gagal update produk.');
            }
            header('Location: ' . BASEURL . 'produk');
            exit;
        }
    }

    // Proses hapus produk
    public function hapus($id) {
        if ($this->model('m_produk')->hapusData($id) > 0) {
            Flasher::setFlash('success', 'Produk berhasil dihapus!');
        } else {
            Flasher::setFlash('error', 'Gagal menghapus produk.');
        }
        header('Location: ' . BASEURL . 'produk');
        exit;
    }

    /**
     * Helper untuk render view admin agar tidak perlu memanggil header/footer manual
     */
    private function renderAdmin($view, $data = []) {
        $this->view('layouts/header-admin', $data);
        $this->view('layouts/sidebar-admin', $data);
        $this->view('admin/' . $view, $data);
        $this->view('layouts/footer-admin', $data);
    }
}