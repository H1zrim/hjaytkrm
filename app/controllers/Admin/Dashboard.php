<?php
require_once '../../app/core/Controller.php';
require_once 'AdminBase.php';

class Dashboard extends AdminBase {

    public function __construct() {
        parent::__construct(); // Memanggil pengecekan login dari AdminBase
    }

    public function index() {
        $data['pageTitle'] = 'Dashboard Admin';
        
        // Contoh: Mengambil statistik untuk ditampilkan di dashboard
        $data['totalPelanggan'] = $this->model('m_pelanggan')->countAll(); // Pastikan fungsi ini ada di m_pelanggan
        $data['totalPesanan']   = $this->model('m_pesanan')->countAll();
        
        $this->view('layouts/header-admin', $data);
        $this->view('layouts/sidebar-admin', $data);
        $this->view('admin/dashboard', $data);
        $this->view('layouts/footer-admin', $data);
    }
}