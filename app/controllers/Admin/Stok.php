<?php

class Stok extends AdminBase {

    public function index() {
        $data['pageTitle']     = 'Kelola Stok';
        $data['halaman_aktif'] = 'stok';
        $search                = isset($_GET['q']) ? trim($_GET['q']) : '';
        $katFilter             = isset($_GET['kat']) ? (int)$_GET['kat'] : 0;
        $showLow               = isset($_GET['low']) && $_GET['low'] === '1';
        $stokModel             = $this->model('m_stok');
        $data['produkList']    = $stokModel->getStokFiltered($search, $katFilter, $showLow);
        $data['kategoriAll']   = $this->model('m_kategori')->getAll();
        $data['lowCount']      = $stokModel->countStokRendah();
        $data['search']        = $search;
        $data['katFilter']     = $katFilter;
        $data['showLow']       = $showLow;
        $this->renderAdmin('stok', $data);
    }

    public function prosesPenyesuaian() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/stok');
            exit;
        }
        $produkId = (int)($_POST['produk_id'] ?? 0);
        $mode     = $_POST['mode'] ?? 'add';
        $adj      = (int)($_POST['adj'] ?? 0);
        $db       = getDB();

        if ($mode === 'set') {
            $db->prepare("UPDATE produk SET stok = ? WHERE id = ?")->execute([max(0, $adj), $produkId]);
        } else {
            $db->prepare("UPDATE produk SET stok = GREATEST(0, stok + ?) WHERE id = ?")->execute([$adj, $produkId]);
        }

        $row = $db->prepare("SELECT nama, stok FROM produk WHERE id = ?");
        $row->execute([$produkId]);
        $p = $row->fetch(PDO::FETCH_ASSOC);
        if ($p) {
            Flasher::setFlash('success', 'Stok "' . htmlspecialchars($p['nama']) . '" diperbarui menjadi ' . (int)$p['stok'] . ' unit.');
        }

        header('Location: ' . BASEURL . 'admin/stok');
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/stok');
            exit;
        }
        $id   = (int)($_POST['id'] ?? 0);
        $stok = max(0, (int)($_POST['stok'] ?? 0));
        if ($this->model('m_stok')->updateJumlahStok($id, $stok)) {
            Flasher::setFlash('success', 'Stok berhasil diperbarui!');
        } else {
            Flasher::setFlash('error', 'Gagal update stok.');
        }
        header('Location: ' . BASEURL . 'admin/stok');
        exit;
    }
}
