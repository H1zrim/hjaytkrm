<?php

class Pembayaran extends AdminBase {

    public function index() {
        $data['pageTitle']     = 'Konfirmasi Pembayaran';
        $data['halaman_aktif'] = 'pembayaran';
        $search                = isset($_GET['q']) ? trim($_GET['q']) : '';
        $activeId              = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $pesananModel          = $this->model('m_pesanan');
        $data['search']        = $search;
        $data['activeId']      = $activeId;
        $data['pesananList']   = $pesananModel->getPesananFiltered('processed', $search);

        if ($activeId) {
            $data['detailPesanan'] = $pesananModel->getById($activeId);
            $data['detailItems']   = $pesananModel->getDetailItems($activeId);
        } else {
            $data['detailPesanan'] = null;
            $data['detailItems']   = [];
        }

        $this->renderAdmin('pembayaran', $data);
    }

    public function konfirmasi($id) {
        if ($this->model('m_pesanan')->updateStatus((int)$id, 'paid')) {
            Flasher::setFlash('success', 'Pembayaran berhasil dikonfirmasi!');
        } else {
            Flasher::setFlash('error', 'Gagal konfirmasi pembayaran.');
        }
        header('Location: ' . BASEURL . 'admin/pembayaran');
        exit;
    }

    public function verifikasi($id) {
        return $this->konfirmasi($id);
    }

    public function tolak($id) {
        if ($this->model('m_pesanan')->batalkanPesanan((int)$id)) {
            Flasher::setFlash('warning', 'Pembayaran ditolak dan pesanan dibatalkan.');
        } else {
            Flasher::setFlash('error', 'Gagal menolak pembayaran.');
        }
        header('Location: ' . BASEURL . 'admin/pembayaran');
        exit;
    }
}
