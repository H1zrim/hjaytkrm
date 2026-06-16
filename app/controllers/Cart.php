<?php

class Cart extends Controller {

    public function index() {
        $cartItems = $_SESSION['cart'] ?? [];
        $subtotal  = array_sum(array_column($cartItems, 'subtotal'));
        $ongkir    = ($subtotal > 0 && $subtotal >= 150000) ? 0 : 15000;
        $diskon    = 0;
        $total     = $subtotal + $ongkir - $diskon;

        $data['pageTitle'] = 'Keranjang Belanja';
        $data['nav_aktif'] = 'home';
        $data['cart']      = $cartItems;
        $data['subtotal']  = $subtotal;
        $data['ongkir']    = $ongkir;
        $data['diskon']    = $diskon;
        $data['total']     = $total;
        $data['promo']     = null;

        $this->view('cart/index', $data);
    }

    public function add($id = null) {
        if (!isset($_SESSION['pelanggan_login']) || $_SESSION['pelanggan_login'] !== true) {
            Flasher::setFlash('error', 'Silakan login terlebih dahulu untuk menambah ke keranjang.');
            header('Location: ' . BASEURL . 'login');
            exit;
        }

        $id  = (int)($id ?? ($_POST['produk_id'] ?? 0));
        $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

        $produk = $this->model('m_produk')->getProdukById($id);
        if (!$produk) {
            Flasher::setFlash('error', 'Produk tidak valid.');
            header('Location: ' . BASEURL . 'catalog');
            exit;
        }

        if ($produk['stok'] < $qty) {
            Flasher::setFlash('error', 'Stok tidak mencukupi. Sisa stok: ' . $produk['stok']);
            header('Location: ' . BASEURL . 'catalog/detail/' . $id);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $newQty = $_SESSION['cart'][$id]['qty'] + $qty;
            if ($newQty > $produk['stok']) {
                Flasher::setFlash('error', 'Jumlah di keranjang melebihi stok yang tersedia (' . $produk['stok'] . ' unit).');
                header('Location: ' . BASEURL . 'cart');
                exit;
            }
            $_SESSION['cart'][$id]['qty']     = $newQty;
            $_SESSION['cart'][$id]['subtotal'] = $newQty * $produk['harga'];
            $_SESSION['cart'][$id]['stok']    = $produk['stok'];
        } else {
            $_SESSION['cart'][$id] = [
                'id'          => $produk['id'],
                'nama_produk' => $produk['nama'],
                'icon'        => $produk['icon'],
                'satuan'      => $produk['satuan'],
                'harga'       => $produk['harga'],
                'stok'        => $produk['stok'],
                'qty'         => $qty,
                'subtotal'    => $produk['harga'] * $qty,
            ];
        }

        Flasher::setFlash('success', '"' . $produk['nama'] . '" berhasil ditambahkan ke keranjang.');
        header('Location: ' . BASEURL . 'cart');
        exit;
    }

    public function update() {
        $key = (int)($_POST['key'] ?? 0);
        $qty = max(1, (int)($_POST['qty'] ?? 1));

        if (isset($_SESSION['cart'][$key])) {
            $produk = $this->model('m_produk')->getProdukById($key);
            if ($produk && $qty <= $produk['stok']) {
                $_SESSION['cart'][$key]['qty']     = $qty;
                $_SESSION['cart'][$key]['subtotal'] = $qty * $_SESSION['cart'][$key]['harga'];
                $_SESSION['cart'][$key]['stok']    = $produk['stok'];
            } else {
                Flasher::setFlash('error', 'Jumlah melebihi stok yang tersedia.');
            }
        }

        header('Location: ' . BASEURL . 'cart');
        exit;
    }

    public function remove() {
        $key = (int)($_POST['key'] ?? 0);
        if (isset($_SESSION['cart'][$key])) {
            $nama = $_SESSION['cart'][$key]['nama_produk'];
            unset($_SESSION['cart'][$key]);
            Flasher::setFlash('success', '"' . $nama . '" dihapus dari keranjang.');
        }
        header('Location: ' . BASEURL . 'cart');
        exit;
    }

    public function clear() {
        unset($_SESSION['cart']);
        Flasher::setFlash('success', 'Keranjang berhasil dikosongkan.');
        header('Location: ' . BASEURL . 'cart');
        exit;
    }

    public function promo() {
        Flasher::setFlash('error', 'Kode promo tidak valid atau sudah habis masa berlakunya.');
        header('Location: ' . BASEURL . 'cart');
        exit;
    }

    public function checkout() {
        header('Location: ' . BASEURL . 'checkout');
        exit;
    }

    public function prosesCheckout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . 'checkout');
            exit;
        }

        if (empty($_SESSION['cart']) || !isset($_SESSION['pelanggan_login'])) {
            header('Location: ' . BASEURL . 'home');
            exit;
        }

        if (empty($_POST['alamat']) || empty($_POST['no_hp'])) {
            Flasher::setFlash('error', 'Alamat pengiriman dan Nomor HP wajib diisi!');
            header('Location: ' . BASEURL . 'checkout');
            exit;
        }

        $subtotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += $item['subtotal'];
        }

        $ongkir = $subtotal >= 150000 ? 0 : 15000;
        $diskon = 0;
        $total  = $subtotal + $ongkir - $diskon;

        $invoice = 'INV-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 5));

        $dataPesanan = [
            'pelanggan_id' => $_SESSION['pelanggan_id'],
            'invoice'      => $invoice,
            'metode_bayar' => $_POST['metode_bayar'] ?? 'transfer',
            'alamat_kirim' => trim($_POST['alamat']),
            'kode_pos'     => trim($_POST['kode_pos'] ?? ''),
            'no_hp'        => trim($_POST['no_hp']),
            'catatan'      => trim($_POST['catatan'] ?? ''),
            'subtotal'     => $subtotal,
            'ongkir'       => $ongkir,
            'diskon'       => $diskon,
            'total'        => $total,
            'status'       => 'pending',
            'items'        => $_SESSION['cart'],
        ];

        $db = getDB();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare(
                "INSERT INTO pesanan (pelanggan_id, invoice, metode_bayar, alamat_kirim, kode_pos, no_hp, catatan, subtotal, ongkir, diskon, total, status, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute([
                $dataPesanan['pelanggan_id'], $dataPesanan['invoice'],
                $dataPesanan['metode_bayar'], $dataPesanan['alamat_kirim'],
                $dataPesanan['kode_pos'],     $dataPesanan['no_hp'],
                $dataPesanan['catatan'],      $dataPesanan['subtotal'],
                $dataPesanan['ongkir'],       $dataPesanan['diskon'],
                $dataPesanan['total'],        $dataPesanan['status'],
            ]);

            $pesananId  = $db->lastInsertId();
            $stmtDetail = $db->prepare(
                "INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, satuan, harga, qty, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmtStok = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");

            foreach ($dataPesanan['items'] as $item) {
                $pReal = $this->model('m_produk')->getProdukById($item['id']);
                if (!$pReal || $pReal['stok'] < $item['qty']) {
                    throw new Exception('Stok produk "' . $item['nama_produk'] . '" tidak mencukupi.');
                }
                $stmtDetail->execute([
                    $pesananId, $item['id'], $item['nama_produk'],
                    $item['satuan'], $item['harga'], $item['qty'], $item['subtotal'],
                ]);
                $stmtStok->execute([$item['qty'], $item['id']]);
            }

            $db->commit();
            unset($_SESSION['cart']);

            Flasher::setFlash('success', 'Pesanan berhasil dibuat! Invoice: ' . $invoice);
            header('Location: ' . BASEURL . 'checkout/success/' . urlencode($invoice));
        } catch (Exception $e) {
            $db->rollBack();
            Flasher::setFlash('error', 'Gagal memproses pesanan: ' . $e->getMessage());
            header('Location: ' . BASEURL . 'checkout');
        }
        exit;
    }
}
