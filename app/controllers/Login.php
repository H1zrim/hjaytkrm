<?php

class Login extends Controller {

    // 1. Halaman Login Pelanggan
    public function index() {
        $this->login(); // Memanggil fungsi login yang sudah ada
    }

    public function login() {
        if (isset($_SESSION['pelanggan_login'])) {
            header('Location: ' . BASEURL . 'home');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->prosesLogin();
        }

        $data['pageTitle'] = 'Login Pelanggan';
        $this->view('auth/login-pelanggan', $data);
    }

    // 2. Proses Login Pelanggan
    public function prosesLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $pelangganModel = $this->model('m_pelanggan');
            
            // Panggil method 'login' yang ada di m_pelanggan.php
            $pelanggan = $pelangganModel->login($email, $password);

            if ($pelanggan) {
                $_SESSION['pelanggan_login'] = true;
                $_SESSION['pelanggan_id']    = $pelanggan['id'];
                $_SESSION['pelanggan_nama']  = $pelanggan['nama_pengguna']; // Sesuaikan dengan kolom di model Anda

                Flasher::setFlash('success', 'Selamat datang, ' . $pelanggan['nama_pengguna'] . '!');
                header('Location: ' . BASEURL . 'home');
            } else {
                Flasher::setFlash('error', 'Email atau Password salah.');
                header('Location: ' . BASEURL . 'auth/login');
            }
            exit;
        }
    }

    // 3. Halaman Registrasi Pelanggan
    public function register() {
        if (isset($_SESSION['pelanggan_login'])) {
            header('Location: ' . BASEURL . 'home');
            exit;
        }

        $data['pageTitle'] = 'Registrasi Akun';
        // Sesuai lokasi folder yang Anda tentukan: app/views/auth/register-pelanggan.php
        $this->view('auth/register-pelanggan', $data);
    }

    // 4. Proses Registrasi Pelanggan
    public function prosesRegister() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validasi Sederhana
        if ($_POST['password'] !== $_POST['konfirmasi']) {
            Flasher::setFlash('error', 'Konfirmasi kata sandi tidak cocok!');
            header('Location: ' . BASEURL . 'auth/register');
            exit;
        }

        if (!isset($_POST['setuju'])) {
            Flasher::setFlash('error', 'Anda harus menyetujui syarat dan ketentuan.');
            header('Location: ' . BASEURL . 'auth/register');
            exit;
        }

        $pelangganModel = $this->model('m_pelanggan');

        if ($pelangganModel->isEmailTerdaftar($_POST['email'])) {
            Flasher::setFlash('error', 'Email sudah terdaftar.');
            header('Location: ' . BASEURL . 'auth/register');
            exit;
        }

        if ($pelangganModel->register($_POST)) {
            Flasher::setFlash('success', 'Registrasi berhasil! Silakan login.');
            header('Location: ' . BASEURL . 'auth/login');
        } else {
            Flasher::setFlash('error', 'Gagal mendaftar ke sistem.');
            header('Location: ' . BASEURL . 'auth/register');
        }
        exit;
    }
}

    // 5. Logout Pelanggan
    public function logout() {
        unset($_SESSION['pelanggan_login']);
        unset($_SESSION['pelanggan_id']);
        unset($_SESSION['pelanggan_nama']);
        
        // Hapus hanya jika tidak ada session lain (misal: cart)
        header('Location: ' . BASEURL . 'home');
        exit;
    }
}