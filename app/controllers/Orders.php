<?php

class Orders extends Controller {
    public function index() {
        if (!isset($_SESSION['pelanggan_login']) || $_SESSION['pelanggan_login'] !== true) {
            Flasher::setFlash('error', 'Silakan login terlebih dahulu untuk melihat pesanan Anda.');
            header('Location: ' . BASEURL . 'login');
            exit;
        }

        $pesananModel = $this->model('m_pesanan');
        $statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'all';
        $data['statusFilter'] = $statusFilter;
        $data['pageTitle']    = 'Pesanan Saya';
        $data['nav_aktif']    = 'orders';

        $data['pesananList'] = $pesananModel->getPesananByPelanggan($_SESSION['pelanggan_id'], $statusFilter);
        $data['statusCounts'] = $pesananModel->getStatusCountsByPelanggan($_SESSION['pelanggan_id']);

        foreach ($data['pesananList'] as &$pesanan) {
            $pesanan['items'] = $pesananModel->getDetailItems($pesanan['id']);
        }

        $this->view('orders/index', $data);
    }

    public function upload() {
        if (!isset($_SESSION['pelanggan_login']) || $_SESSION['pelanggan_login'] !== true) {
            Flasher::setFlash('error', 'Silakan login terlebih dahulu.');
            header('Location: ' . BASEURL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'orders');
            exit;
        }

        $pesananId = (int)($_POST['pesanan_id'] ?? 0);
        $pesananModel = $this->model('m_pesanan');
        $pesanan = $pesananModel->getById($pesananId);

        if (!$pesanan || $pesanan['pelanggan_id'] != $_SESSION['pelanggan_id']) {
            Flasher::setFlash('error', 'Pesanan tidak ditemukan atau akses ditolak.');
            header('Location: ' . BASEURL . 'orders');
            exit;
        }

        if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
            Flasher::setFlash('error', 'Unggah bukti pembayaran terlebih dahulu.');
            header('Location: ' . BASEURL . 'orders');
            exit;
        }

        $allowed = ['image/jpeg', 'image/png', 'application/pdf', 'image/webp'];
        if (!in_array($_FILES['bukti']['type'], $allowed, true)) {
            Flasher::setFlash('error', 'Format file tidak didukung. Gunakan JPG, PNG, WEBP, atau PDF.');
            header('Location: ' . BASEURL . 'orders');
            exit;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/bukti/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = 'bukti_' . $pesananId . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['bukti']['name']));
        $destination = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $destination)) {
            Flasher::setFlash('error', 'Gagal menyimpan file bukti pembayaran.');
            header('Location: ' . BASEURL . 'orders');
            exit;
        }

        $relativePath = 'uploads/bukti/' . $fileName;
        if ($pesananModel->updateBuktiBayar($pesananId, $relativePath)) {
            Flasher::setFlash('success', 'Bukti pembayaran berhasil diunggah. Tunggu konfirmasi admin.');
        } else {
            Flasher::setFlash('error', 'Gagal menyimpan bukti pembayaran ke sistem.');
        }

        header('Location: ' . BASEURL . 'orders');
        exit;
    }
}
