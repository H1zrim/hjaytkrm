<?php

class Pembayaran extends Controller {

    public function __construct() {
        if (!isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'auth/loginadmin');
            exit;
        }
    }

    // Menampilkan daftar pesanan yang menunggu verifikasi pembayaran
    public function index() {
        $data['pageTitle'] = 'Verifikasi Pembayaran';
        // Ambil pesanan yang statusnya 'pending' atau 'menunggu_verifikasi'
        $data['pesanan'] = $this->model('m_pesanan')->getPesananByStatus('pending');
        
        $this->renderAdmin('pembayaran', $data);
    }

    // Proses Verifikasi Pembayaran (Admin klik "Terima")
    public function verifikasi($id) {
        if ($this->model('m_pesanan')->updateStatus($id, 'paid')) {
            Flasher::setFlash('success', 'Pembayaran berhasil diverifikasi!');
        } else {
            Flasher::setFlash('error', 'Gagal verifikasi pembayaran.');
        }
        header('Location: ' . BASEURL . 'pembayaran');
        exit;
    }

    private function renderAdmin($view, $data = []) {
        $this->view('layouts/header-admin', $data);
        $this->view('layouts/sidebar-admin', $data);
        $this->view('admin/' . $view, $data);
        $this->view('layouts/footer-admin', $data);
    }
}