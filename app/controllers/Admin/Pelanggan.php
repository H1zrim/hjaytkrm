<?php

class Pelanggan extends AdminBase {

    public function index() {
        $data['pageTitle']     = 'Data Pelanggan';
        $data['halaman_aktif'] = 'pelanggan';
        $search                = isset($_GET['q']) ? trim($_GET['q']) : '';
        $data['pelangganList'] = $this->model('m_pelanggan')->getAllWithStats($search);
        $data['search']        = $search;
        $this->renderAdmin('pelanggan', $data);
    }

    public function detail($id) {
        $data['pageTitle']     = 'Detail Pelanggan';
        $data['halaman_aktif'] = 'pelanggan';
        $data['user']          = $this->model('m_pelanggan')->getPelangganById((int)$id);
        $data['riwayat']       = $this->model('m_pesanan')->getPesananByPelanggan((int)$id);
        $this->renderAdmin('pelanggan', $data);
    }

    public function hapus($id) {
        if ($this->model('m_pelanggan')->hapusData((int)$id)) {
            Flasher::setFlash('success', 'Pelanggan berhasil dihapus!');
        } else {
            Flasher::setFlash('error', 'Gagal menghapus pelanggan.');
        }
        header('Location: ' . BASEURL . 'admin/pelanggan');
        exit;
    }
}
