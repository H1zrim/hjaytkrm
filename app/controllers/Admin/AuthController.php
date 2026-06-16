<?php
require_once '../../app/core/Controller.php';

class Auth extends Controller {
    
    // Halaman Login Admin
    public function login() {
        if (isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'admin/dashboard');
            exit;
        }
        $data['pageTitle'] = 'Login Admin';
        $this->view('auth/login-admin', $data);
    }

    // Proses Validasi Login
    public function prosesLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/auth/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Asumsi Anda punya model m_admin
        $admin = $this->model('m_admin')->getAdminByEmail($email);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_login'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            
            header('Location: ' . BASEURL . 'admin/dashboard');
            exit;
        }

        Flasher::setFlash('error', 'Email atau Password salah!');
        header('Location: ' . BASEURL . 'admin/auth/login');
        exit;
    }

    // Logout
    public function logout() {
        session_destroy();
        header('Location: ' . BASEURL . 'admin/auth/login');
        exit;
    }
}