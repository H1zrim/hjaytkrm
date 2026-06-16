<?php

class Profile extends Controller {
    public function __construct() {
        if (!isset($_SESSION['pelanggan_login']) || $_SESSION['pelanggan_login'] !== true) {
            Flasher::setFlash('error', 'Silakan login terlebih dahulu untuk mengakses profil.');
            header('Location: ' . BASEURL . 'auth/login');
            exit;
        }
    }

    public function index() {
        $pelangganModel = $this->model('m_pelanggan');
        $pelanggan = $pelangganModel->getPelangganById($_SESSION['pelanggan_id']);

        $activeTab = isset($_GET['tab']) ? trim($_GET['tab']) : 'profil';
        $data['pageTitle'] = 'Profil Saya';
        $data['pelanggan'] = $pelanggan;
        $data['activeTab'] = in_array($activeTab, ['profil', 'password'], true) ? $activeTab : 'profil';
        $data['totalPesanan'] = 0;
        $data['totalBelanja'] = 0;

        $pesananModel = $this->model('m_pesanan');
        $statusCounts = $pesananModel->getStatusCountsByPelanggan($_SESSION['pelanggan_id']);
        $data['totalPesanan'] = array_sum($statusCounts);

        $pesananList = $pesananModel->getPesananByPelanggan($_SESSION['pelanggan_id']);
        foreach ($pesananList as $item) {
            $data['totalBelanja'] += (float)$item['total'];
        }

        $this->view('profile/index', $data);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'profile');
            exit;
        }

        $pelangganModel = $this->model('m_pelanggan');
        $success = $pelangganModel->updateProfil($_SESSION['pelanggan_id'], $_POST);

        if ($success) {
            Flasher::setFlash('success', 'Profil berhasil disimpan.');
        } else {
            Flasher::setFlash('error', 'Gagal memperbarui profil.');
        }

        header('Location: ' . BASEURL . 'profile?tab=profil');
        exit;
    }

    public function changepassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'profile');
            exit;
        }

        $oldPassword = $_POST['password_lama'] ?? '';
        $newPassword = $_POST['password_baru'] ?? '';
        $confirmNew = $_POST['konfirmasi_baru'] ?? '';

        if (trim($newPassword) === '' || trim($oldPassword) === '' || trim($confirmNew) === '') {
            Flasher::setFlash('error', 'Semua bidang kata sandi harus diisi.');
            header('Location: ' . BASEURL . 'profile?tab=password');
            exit;
        }

        if ($newPassword !== $confirmNew) {
            Flasher::setFlash('error', 'Kata sandi baru dan konfirmasi tidak cocok.');
            header('Location: ' . BASEURL . 'profile?tab=password');
            exit;
        }

        $pelangganModel = $this->model('m_pelanggan');
        $pelanggan = $pelangganModel->getPelangganById($_SESSION['pelanggan_id']);

        if (!$pelanggan) {
            Flasher::setFlash('error', 'Akun tidak ditemukan.');
            header('Location: ' . BASEURL . 'profile?tab=password');
            exit;
        }

        $stmt = getDB()->prepare('SELECT password FROM pelanggan WHERE id = ?');
        $stmt->execute([$_SESSION['pelanggan_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($oldPassword, $row['password'])) {
            Flasher::setFlash('error', 'Kata sandi lama salah.');
            header('Location: ' . BASEURL . 'profile?tab=password');
            exit;
        }

        if ($pelangganModel->updatePassword($_SESSION['pelanggan_id'], $newPassword)) {
            Flasher::setFlash('success', 'Kata sandi berhasil diperbarui.');
        } else {
            Flasher::setFlash('error', 'Gagal memperbarui kata sandi.');
        }

        header('Location: ' . BASEURL . 'profile?tab=password');
        exit;
    }
}
