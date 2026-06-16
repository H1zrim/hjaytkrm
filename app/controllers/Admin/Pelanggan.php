<?php
require_once 'app/controllers/Controller.php';
require_once 'app/core/App.php';
class Pelanggan extends AdminBase {

    public function __construct() {
        parent::__construct();
    }

    // Menampilkan daftar semua pelanggan
    public function index() {
        $data['pageTitle'] = 'Data Pelanggan';
        $data['pelanggan'] = $this->model('m_pelanggan')->getAll();
        $this->view('admin/pelanggan', $data);
    }

    // Melihat detail aktivitas pelanggan
    public function detail($id) {
        $data['pageTitle'] = 'Detail Pelanggan';
        $data['user'] = $this->model('m_pelanggan')->getPelangganById($id);
        // Bisa tambahkan riwayat pesanan pelanggan tersebut di sini
        $data['riwayat'] = $this->model('m_pesanan')->getPesananByPelanggan($id);
        $this->view('admin/pelanggan_detail', $data);
    }

    // Hapus pelanggan (hati-hati dengan constraint database)
    public function hapus($id) {
        if ($this->model('m_pelanggan')->hapusData($id) > 0) {
            Flasher::setFlash('success', 'Pelanggan berhasil dihapus!');
        } else {
            Flasher::setFlash('error', 'Gagal menghapus pelanggan.');
        }
        header('Location: ' . BASEURL . 'admin/pelanggan');
        exit;
    }
}