<?php

class Home extends Controller {

    // Halaman Beranda / Katalog Utama
    public function index() {
        $data['pageTitle'] = 'Katalog Toko Online';
        
        // Menangkap filter pencarian dan kategori dari user
        $search    = isset($_GET['q']) ? trim($_GET['q']) : '';
        $katFilter = isset($_GET['kat']) ? (int)$_GET['kat'] : 0;

        // Mengambil data produk & kategori dari Model yang sudah kita buat
        $data['produkList']  = $this->model('m_produk')->getProdukFiltered($search, $katFilter);
        $data['kategoriAll'] = $this->model('m_kategori')->getAllKategori();
        
        $data['search']    = $search;
        $data['katFilter'] = $katFilter;

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
        $this->view('detail/index', $data);
    }
}