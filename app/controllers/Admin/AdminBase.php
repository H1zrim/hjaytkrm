<?php
class AdminBase extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_login'])) {
            header('Location: ' . BASEURL . 'auth/loginadmin');
            exit;
        }
    }
    public function view($view, $data = []) {
    // Pastikan path ini selalu dari folder 'app/views/'
    require_once '../app/views/' . $view . '.php';
}
}