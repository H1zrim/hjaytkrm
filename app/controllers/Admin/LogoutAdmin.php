<?php

class LogoutAdmin extends Controller {
    public function index() {
        if (isset($_SESSION['admin_login'])) {
            unset($_SESSION['admin_login']);
            unset($_SESSION['admin_id']);
            unset($_SESSION['admin_nama']);
        }

        // Direct logout action for admin route
        header('Location: ' . BASEURL . 'auth/loginadmin');
        exit;
    }
}
