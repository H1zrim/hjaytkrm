<?php

class Kategori extends AdminBase {

    public function index() {
        $data['pageTitle']     = 'Data Kategori';
        $data['halaman_aktif'] = 'kategori';
        $data['kategoriList']  = $this->model('m_kategori')->getAll();
        $data['editData']      = (isset($_GET['edit']) && (int)$_GET['edit'] > 0)
                                 ? $this->model('m_kategori')->getKategoriById((int)$_GET['edit'])
                                 : null;
        $this->renderAdmin('kategori', $data);
    }

    public function prosesTambah() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/kategori');
            exit;
        }
        if ($this->model('m_kategori')->tambahData($_POST)) {
            Flasher::setFlash('success', 'Kategori berhasil ditambahkan!');
        } else {
            Flasher::setFlash('error', 'Gagal menambah kategori.');
        }
        header('Location: ' . BASEURL . 'admin/kategori');
        exit;
    }

    public function prosesEdit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/kategori');
            exit;
        }
        if ($this->model('m_kategori')->updateData($_POST)) {
            Flasher::setFlash('success', 'Kategori berhasil diupdate!');
        } else {
            Flasher::setFlash('error', 'Gagal update kategori.');
        }
        header('Location: ' . BASEURL . 'admin/kategori');
        exit;
    }

    public function hapus($id) {
        if ($this->model('m_kategori')->hapusData((int)$id)) {
            Flasher::setFlash('success', 'Kategori berhasil dihapus!');
        } else {
            Flasher::setFlash('error', 'Gagal menghapus kategori.');
        }
        header('Location: ' . BASEURL . 'admin/kategori');
        exit;
    }
}
