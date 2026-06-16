<?php

class Produk extends AdminBase {

    public function index() {
        $data['pageTitle']     = 'Data Produk';
        $data['halaman_aktif'] = 'produk';
        $search                = isset($_GET['q']) ? trim($_GET['q']) : '';
        $katFilter             = isset($_GET['kat']) ? (int)$_GET['kat'] : 0;
        $data['produkList']    = $this->model('m_produk')->getProdukFiltered($search, $katFilter);
        $data['kategoriAll']   = $this->model('m_kategori')->getAll();
        $data['search']        = $search;
        $data['katFilter']     = $katFilter;
        $this->renderAdmin('produk', $data);
    }

    public function tambah() {
        $data['pageTitle']     = 'Tambah Produk';
        $data['halaman_aktif'] = 'produk';
        $data['kategori']      = $this->model('m_kategori')->getAll();
        $this->renderAdmin('produk-tambah', $data);
    }

    public function prosesTambah() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/produk');
            exit;
        }

        $gambar = '';
        if (!empty($_FILES['foto']['name'])) {
            $gambar = $this->uploadFoto();
            if ($gambar === false) {
                Flasher::setFlash('error', 'Format foto tidak valid. Gunakan JPG/PNG/WEBP.');
                header('Location: ' . BASEURL . 'admin/produk/tambah');
                exit;
            }
        }

        $_POST['foto'] = $gambar;
        if ($this->model('m_produk')->tambahData($_POST)) {
            Flasher::setFlash('success', 'Produk berhasil ditambahkan!');
        } else {
            Flasher::setFlash('error', 'Gagal menambahkan produk.');
        }
        header('Location: ' . BASEURL . 'admin/produk');
        exit;
    }

    public function edit($id) {
        $data['pageTitle']     = 'Edit Produk';
        $data['halaman_aktif'] = 'produk';
        $data['kategori']      = $this->model('m_kategori')->getAll();
        $data['produk']        = $this->model('m_produk')->getById((int)$id);
        $this->renderAdmin('produk-edit', $data);
    }

    public function prosesEdit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/produk');
            exit;
        }

        if (!empty($_FILES['foto']['name'])) {
            $gambar = $this->uploadFoto();
            if ($gambar === false) {
                Flasher::setFlash('error', 'Format foto tidak valid. Gunakan JPG/PNG/WEBP.');
                header('Location: ' . BASEURL . 'admin/produk/edit/' . (int)$_POST['id']);
                exit;
            }
            $_POST['foto'] = $gambar;
        }

        if ($this->model('m_produk')->updateData($_POST)) {
            Flasher::setFlash('success', 'Produk berhasil diupdate!');
        } else {
            Flasher::setFlash('error', 'Gagal update produk.');
        }
        header('Location: ' . BASEURL . 'admin/produk');
        exit;
    }

    public function hapus($id) {
        if ($this->model('m_produk')->hapusData((int)$id)) {
            Flasher::setFlash('success', 'Produk berhasil dihapus!');
        } else {
            Flasher::setFlash('error', 'Gagal menghapus produk.');
        }
        header('Location: ' . BASEURL . 'admin/produk');
        exit;
    }

    private function uploadFoto() {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($_FILES['foto']['type'], $allowed, true)) {
            return false;
        }
        $ext     = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $name    = 'produk_' . time() . '.' . $ext;
        $destDir = __DIR__ . '/../../../public/images/';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        move_uploaded_file($_FILES['foto']['tmp_name'], $destDir . $name);
        return $name;
    }
}
