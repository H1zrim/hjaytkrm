<?php

class Pesanan extends AdminBase {

    public function index() {
        $data['pageTitle']     = 'Manajemen Pesanan';
        $data['halaman_aktif'] = 'pesanan';
        $statusFilter          = isset($_GET['status']) ? trim($_GET['status']) : '';
        $search                = isset($_GET['q']) ? trim($_GET['q']) : '';
        $data['statusFilter']  = $statusFilter;
        $data['search']        = $search;
        $data['pesananList']   = $this->model('m_pesanan')->getPesananFiltered($statusFilter, $search);
        $data['statusCounts']  = $this->model('m_pesanan')->getStatusCounts();
        $this->renderAdmin('pesanan', $data);
    }

    public function detail($id) {
        $data['pageTitle']     = 'Detail Pesanan';
        $data['halaman_aktif'] = 'pesanan';
        $data['pesanan']       = $this->model('m_pesanan')->getById((int)$id);
        $data['items']         = $this->model('m_pesanan')->getDetailItems((int)$id);
        $this->renderAdmin('pesanan_detail', $data);
    }

    public function prosesAksi($aksi, $id) {
        $id    = (int)$id;
        $model = $this->model('m_pesanan');
        if ($aksi === 'konfirmasi') {
            $model->updateStatus($id, 'processed');
            Flasher::setFlash('success', 'Pesanan dikonfirmasi, menunggu verifikasi pembayaran.');
        } elseif ($aksi === 'batalkan') {
            $model->batalkanPesanan($id);
            Flasher::setFlash('success', 'Pesanan dibatalkan dan stok dikembalikan.');
        }
        header('Location: ' . BASEURL . 'admin/pesanan');
        exit;
    }

    public function detailAjax($id) {
        $pesanan = $this->model('m_pesanan')->getById((int)$id);
        $items   = $this->model('m_pesanan')->getDetailItems((int)$id);
        if (!$pesanan) {
            echo '<div class="alert alert-danger">Pesanan tidak ditemukan.</div>';
            exit;
        }
        $statusLabels = ['pending' => 'Menunggu', 'processed' => 'Diproses', 'paid' => 'Lunas', 'cancelled' => 'Dibatalkan'];
        $met          = ['qris' => '📱 QRIS', 'transfer' => '🏦 Transfer', 'cod' => '💵 COD'];
        echo '<div style="font-size:13px;">';
        echo '<p style="margin-bottom:6px;"><strong>Invoice:</strong> ' . htmlspecialchars($pesanan['invoice']) . '</p>';
        echo '<p style="margin-bottom:6px;"><strong>Pelanggan:</strong> ' . htmlspecialchars($pesanan['nama_pengguna']) . '</p>';
        echo '<p style="margin-bottom:6px;"><strong>Metode:</strong> ' . htmlspecialchars($met[$pesanan['metode_bayar']] ?? $pesanan['metode_bayar']) . '</p>';
        echo '<p style="margin-bottom:6px;"><strong>Alamat:</strong> ' . htmlspecialchars($pesanan['alamat_kirim'] ?? '-') . '</p>';
        echo '<p style="margin-bottom:10px;"><strong>Status:</strong> <span class="badge badge-' . htmlspecialchars($pesanan['status']) . '">' . htmlspecialchars($statusLabels[$pesanan['status']] ?? $pesanan['status']) . '</span></p>';
        echo '<hr style="border-color:rgba(201,169,110,.2);margin:10px 0;">';
        foreach ($items as $item) {
            echo '<div style="display:flex;justify-content:space-between;padding:5px 0;">';
            echo '<span>' . htmlspecialchars($item['icon'] ?? '📦') . ' ' . htmlspecialchars($item['nama_produk']) . ' ×' . (int)$item['qty'] . '</span>';
            echo '<strong>Rp ' . number_format($item['subtotal'], 0, ',', '.') . '</strong>';
            echo '</div>';
        }
        echo '<hr style="border-color:rgba(201,169,110,.2);margin:10px 0;">';
        echo '<div style="display:flex;justify-content:space-between;font-size:14px;">';
        echo '<strong>Total</strong>';
        echo '<strong style="color:var(--accent-warm);">Rp ' . number_format($pesanan['total'], 0, ',', '.') . '</strong>';
        echo '</div></div>';
        exit;
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/pesanan');
            exit;
        }
        $id     = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        if ($this->model('m_pesanan')->updateStatus($id, $status)) {
            Flasher::setFlash('success', 'Status pesanan berhasil diupdate!');
        } else {
            Flasher::setFlash('error', 'Gagal update status.');
        }
        header('Location: ' . BASEURL . 'admin/pesanan/detail/' . $id);
        exit;
    }

    public function batal($id) {
        if ($this->model('m_pesanan')->batalkanPesanan((int)$id)) {
            Flasher::setFlash('success', 'Pesanan berhasil dibatalkan dan stok dikembalikan.');
        } else {
            Flasher::setFlash('error', 'Gagal membatalkan pesanan.');
        }
        header('Location: ' . BASEURL . 'admin/pesanan');
        exit;
    }
}
