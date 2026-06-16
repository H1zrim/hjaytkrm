<?php

class Auth extends Controller {

    // 1. Halaman Login
    public function index() {
        if (isset($_SESSION['pelanggan_login'])) {
            header('Location: ' . BASEURL . 'home');
            exit;
        }
        $data['pageTitle'] = 'Login Pelanggan';
        $data['nav_aktif'] = 'home';
        $this->view('auth/login-pelanggan', $data);
    }

    // 2. Proses Login
    public function prosesLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'auth');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $pelanggan = $this->model('m_pelanggan')->login($email, $password);

        if ($pelanggan) {
            $_SESSION['pelanggan_login'] = true;
            $_SESSION['pelanggan_id']    = $pelanggan['id'];
            $_SESSION['pelanggan_nama']  = $pelanggan['nama_pengguna']; 

            Flasher::setFlash('success', 'Selamat datang, ' . $pelanggan['nama_pengguna'] . '!');
            header('Location: ' . BASEURL . 'home');
        } else {
            Flasher::setFlash('error', 'Email atau Password salah.');
            header('Location: ' . BASEURL . 'auth');
        }
        exit;
    }

    // 3. Halaman Registrasi
    public function register() {
        if (isset($_SESSION['pelanggan_login'])) {
            header('Location: ' . BASEURL . 'home');
            exit;
        }
        $data['pageTitle'] = 'Registrasi Akun';
        $data['nav_aktif'] = 'home';
        $this->view('auth/register-pelanggan', $data);
    }

    // 4. Proses Registrasi
    public function prosesRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'auth/register');
            exit;
        }

        $pelangganModel = $this->model('m_pelanggan');

        // Validasi
        if ($_POST['password'] !== $_POST['konfirmasi']) {
            Flasher::setFlash('error', 'Konfirmasi kata sandi tidak cocok!');
        } elseif ($pelangganModel->isEmailTerdaftar($_POST['email'])) {
            Flasher::setFlash('error', 'Email sudah terdaftar.');
        } else {
            // Jalankan Registrasi
            if ($pelangganModel->register($_POST) > 0) {
                Flasher::setFlash('success', 'Registrasi berhasil! Silakan login.');
                header('Location: ' . BASEURL . 'auth');
                exit;
            } else {
                Flasher::setFlash('error', 'Gagal mendaftar ke sistem.');
            }
        }
        
        header('Location: ' . BASEURL . 'auth/register');
        exit;
    }

    // 5. Logout
    public function logout() {
        // Hancurkan session dengan aman
        session_unset();
        session_destroy();
        
        header('Location: ' . BASEURL . 'home');
        exit;
    }
}