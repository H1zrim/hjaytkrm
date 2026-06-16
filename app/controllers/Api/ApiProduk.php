<?php

class ApiProduk extends ApiBase {

    /** GET /api/produk?q=&kat=&limit= */
    public function index(): void {
        $search    = isset($_GET['q'])     ? trim($_GET['q'])    : '';
        $katFilter = isset($_GET['kat'])   ? (int)$_GET['kat']  : 0;
        $limit     = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;

        $produkList = $this->model('m_produk')->getProdukFiltered($search, $katFilter);

        if ($limit > 0) {
            $produkList = array_slice($produkList, 0, $limit);
        }

        foreach ($produkList as &$p) {
            $p['foto_url'] = !empty($p['foto']) ? BASEURL . 'uploads/produk/' . $p['foto'] : null;
        }
        unset($p);

        $this->success([
            'total'  => count($produkList),
            'produk' => $produkList,
        ]);
    }

    /** GET /api/produk/detail/{id} */
    public function detail(int $id = 0): void {
        if ($id <= 0) {
            $this->error('ID produk tidak valid.');
        }

        $produk = $this->model('m_produk')->getProdukById($id);
        if (!$produk) {
            $this->error('Produk tidak ditemukan.', 404);
        }

        $produk['foto_url'] = !empty($produk['foto']) ? BASEURL . 'uploads/produk/' . $produk['foto'] : null;

        $this->success($produk);
    }
}
