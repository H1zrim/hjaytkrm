<?php

class Catalog extends Controller {

    public function index() {
        $data['pageTitle'] = 'Katalog Produk';
        $data['nav_aktif'] = 'catalog';

        $search    = isset($_GET['q']) ? trim($_GET['q']) : '';
        $katFilter = isset($_GET['kat']) ? (int)$_GET['kat'] : 0;
        $sort      = isset($_GET['sort']) ? trim($_GET['sort']) : 'default';

        $produkList = $this->model('m_produk')->getProdukFiltered($search, $katFilter);

        if ($sort === 'price_asc') {
            usort($produkList, fn($a, $b) => $a['harga'] <=> $b['harga']);
        } elseif ($sort === 'price_desc') {
            usort($produkList, fn($a, $b) => $b['harga'] <=> $a['harga']);
        } elseif ($sort === 'name') {
            usort($produkList, fn($a, $b) => strcmp($a['nama'], $b['nama']));
        }

        $data['produkList']  = $produkList;
        $data['kategoriAll'] = $this->model('m_kategori')->getAllKategori();
        $data['search']      = $search;
        $data['katFilter']   = $katFilter;
        $data['sort']        = $sort;

        $this->view('catalog/index', $data);
    }

    public function detail($id) {
        $id      = (int)$id;
        $produk  = $this->model('m_produk')->getProdukById($id);

        if (!$produk) {
            Flasher::setFlash('error', 'Produk tidak ditemukan atau sudah tidak tersedia.');
            header('Location: ' . BASEURL . 'catalog');
            exit;
        }

        $data['pageTitle'] = $produk['nama'];
        $data['nav_aktif'] = 'catalog';
        $data['produk']    = $produk;

        $this->view('detail/index', $data);
    }
}
