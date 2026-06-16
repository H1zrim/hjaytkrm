<?php

class Login extends Controller {

    public function index() {
        if (isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'admin/dashboard');
            exit;
        }
        $data['pageTitle'] = 'Login Admin';
        $this->view('auth/login-admin', $data);
    }

    public function prosesLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'admin/login');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $admin = $this->model('m_admin')->getAdminByEmail($email);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_login'] = true;
            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_nama']  = $admin['nama'];

            header('Location: ' . BASEURL . 'admin/dashboard');
            exit;
        }

        Flasher::setFlash('error', 'Email atau Password salah!');
        header('Location: ' . BASEURL . 'admin/login');
        exit;
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASEURL . 'admin/login');
        exit;
    }

    public function view($view, $data = []) {
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            extract($data);
            require_once $viewPath;
        } else {
            die('View tidak ditemukan: ' . htmlspecialchars($viewPath));
        }
    }
}
