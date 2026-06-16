<?php

class Dashboard extends AdminBase {

    public function index() {
        $pesananModel = $this->model('m_pesanan');
        $stokModel    = $this->model('m_stok');

        $data['pageTitle']        = 'Dashboard';
        $data['halaman_aktif']    = 'dashboard';
        $data['admin_nama']       = $_SESSION['admin_nama'] ?? 'Admin';
        $data['totalPelanggan']   = $this->model('m_pelanggan')->countAll();
        $data['totalPesanan']     = $pesananModel->countAll();
        $data['totalOmzet']       = $pesananModel->getTotalOmzet();
        $data['totalProduk']      = count($this->model('m_produk')->getAll());
        $data['stokRendah']       = $stokModel->countStokRendah();
        $data['pendingPesanan']   = $pesananModel->countByStatus('pending');
        $data['processedPesanan'] = $pesananModel->countByStatus('processed');
        $data['recentOrders']     = array_slice($pesananModel->getPesananFiltered(), 0, 5);
        $data['lowStok']          = array_slice($stokModel->getStokFiltered('', 0, true), 0, 5);
        $data['kategoriSales']    = $this->getKategoriSales();

        $this->renderAdmin('dashboard', $data);
    }

    private function getKategoriSales() {
        $stmt = getDB()->query(
            "SELECT k.nama_kategori, COALESCE(SUM(dp.qty), 0) AS total_terjual
             FROM kategori k
             LEFT JOIN produk pr ON pr.kategori_id = k.id
             LEFT JOIN detail_pesanan dp ON dp.produk_id = pr.id
             LEFT JOIN pesanan p ON p.id = dp.pesanan_id AND p.status = 'paid'
             GROUP BY k.id, k.nama_kategori
             ORDER BY total_terjual DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
