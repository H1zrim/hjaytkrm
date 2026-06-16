<?php

class Home extends Controller {

    // Halaman Beranda / Katalog Utama
    public function index() {
        $data['pageTitle'] = 'Beranda';
        $data['nav_aktif'] = 'home';

        $data['produkUnggulan'] = $this->model('m_produk')->getProdukFiltered('', 0);
        $data['kategoriList']   = $this->model('m_kategori')->getAllKategori();

        $this->view('home/index', $data);
    }

    // Halaman Detail Produk Spesifik
    public function detail($id) {
        $id = (int)$id;
        $produkModel = $this->model('m_produk');
        
        $data['produk'] = $produkModel->getProdukById($id);

        if (!$data['produk']) {
            Flasher::setFlash('error', 'Produk tidak ditemukan atau sudah tidak tersedia.');
            header('Location: ' . BASEURL . 'home');
            exit;
        }

        $data['pageTitle'] = $data['produk']['nama'];
        $data['nav_aktif'] = 'catalog';
        $this->view('detail/index', $data);
    }
}