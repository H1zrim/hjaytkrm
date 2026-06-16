<?php

class Checkout extends Controller {
    public function index() {
        if (!isset($_SESSION['pelanggan_login']) || $_SESSION['pelanggan_login'] !== true) {
            Flasher::setFlash('error', 'Silakan login terlebih dahulu untuk melakukan checkout.');
            header('Location: ' . BASEURL . 'auth/login');
            exit;
        }

        if (empty($_SESSION['cart'])) {
            Flasher::setFlash('error', 'Keranjang belanja Anda masih kosong.');
            header('Location: ' . BASEURL . 'home');
            exit;
        }

        $cartItems = $_SESSION['cart'];
        $subtotal  = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['subtotal'];
        }

        $data['pageTitle'] = 'Checkout Pesanan';
        $data['pelanggan'] = $this->model('m_pelanggan')->getPelangganById($_SESSION['pelanggan_id']);
        $data['cart']      = $cartItems;
        $data['subtotal']  = $subtotal;
        $data['ongkir']    = 15000;
        $data['diskon']    = 0;
        $data['total']     = $subtotal + $data['ongkir'] - $data['diskon'];
        $data['input']     = [];

        $this->view('checkout/index', $data);
    }

    public function proses() {
        $cartController = new Cart();
        return $cartController->prosesCheckout();
    }

    public function success($invoice = '') {
        if (empty($invoice)) {
            header('Location: ' . BASEURL . 'home');
            exit;
        }

        $pesananModel = $this->model('m_pesanan');
        $pesanan = $pesananModel->getByInvoice(urldecode($invoice));

        if (!$pesanan) {
            Flasher::setFlash('error', 'Data pesanan tidak ditemukan.');
            header('Location: ' . BASEURL . 'orders');
            exit;
        }

        $data['pageTitle'] = 'Checkout Berhasil';
        $data['pesanan']   = $pesanan;

        $this->view('checkout/success', $data);
    }
}
