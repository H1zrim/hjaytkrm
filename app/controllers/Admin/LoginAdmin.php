<?php
class LoginAdmin extends Controller {
    public function index() {
        // Jika sudah login, lempar ke dashboard
        if (isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'dashboardadmin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->prosesLogin();
        }

        $this->view('auth/login-admin');
    }

    public function prosesLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $admin = $this->model('m_admin')->getAdminByEmail($email);

        $isValidPassword = false;
        if ($admin) {
            $isValidPassword = password_verify($password, $admin['password']) || $password === $admin['password'];
        }

        if ($admin && $isValidPassword) {
            $_SESSION['admin_login'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            
            header('Location: ' . BASEURL . 'dashboardadmin');
            exit;
        }

        Flasher::setFlash('error', 'Email atau Kata Sandi salah!');
        header('Location: ' . BASEURL . 'auth/loginadmin');
        exit;
    }
}