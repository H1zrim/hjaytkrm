<?php

class Pelanggan extends Controller {

    // Constructor proteksi agar hanya pelanggan terautentikasi yang bisa masuk
    public function __construct() {
        if (!isset($_SESSION['pelanggan_login']) || $_SESSION['pelanggan_login'] !== true) {
            Flasher::setFlash('error', 'Akses ditolak. Silakan login terlebih dahulu.');
            header('Location: ' . BASEURL . 'login');
            exit;
        }
    }

    // Dashboard profil pelanggan
    public function index() {
        $data['pageTitle'] = 'Akun Saya';
        $data['user'] = $this->model('m_pelanggan')->getPelangganById($_SESSION['pelanggan_id']);
        
        $this->view('client/dashboard', $data);
    }

    // Melihat daftar riwayat transaksi pesanan milik pelanggan terkait
    public function pesananSaya() {
        $data['pageTitle'] = 'Riwayat Pesanan Anda';
        
        // Kita gunakan model m_pesanan yang sudah ada, namun dengan query khusus ID pelanggan terkait
        // Anda dapat menambahkan fungsi helper kecil di m_pesanan untuk memfilter berdasarkan pelanggan_id
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pesanan WHERE pelanggan_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['pelanggan_id']]);
        $data['pesananList'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('client/pesanan_riwayat', $data);
    }

    // Detail rincian pesanan spesifik untuk sisi pelanggan
    public function detailPesanan($id) {
        $id = (int)$id;
        $pesananModel = $this->model('m_pesanan');

        $data['pesanan'] = $pesananModel->getPesananById($id);

        // Keamanan: Pastikan pelanggan tidak mengintip invoice pelanggan lain melalui manipulasi ID di URL
        if (!$data['pesanan'] || $data['pesanan']['pelanggan_id'] != $_SESSION['pelanggan_id']) {
            Flasher::setFlash('error', 'Data transaksi tidak ditemukan atau hak akses ditolak.');
            header('Location: ' . BASEURL . 'customer/pesananSaya');
            exit;
        }

        $data['pageTitle'] = 'Detail Transaksi ' . $data['pesanan']['invoice'];
        $data['items'] = $pesananModel->getDetailItems($id);

        $this->view('admin/pesanan_detail', $data);
    }
}