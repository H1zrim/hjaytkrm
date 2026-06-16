<?php

class Cart extends Controller {

    // Menampilkan halaman keranjang belanja
    public function index() {
        $data['pageTitle'] = 'Keranjang Belanja Anda';
        
        // Mengambil data keranjang dari session (jika ada)
        $data['cartItems'] = $_SESSION['cart'] ?? [];
        
        $this->view('client/cart', $data);
    }

    /**
     * Memproses penambahan produk ke dalam keranjang
     * URL Rujukan: BASEURL . 'cart/add/[ID_PRODUK]'
     */
    public function add($id = null) {
        $id = (int)($id ?? ($_POST['produk_id'] ?? 0));
        $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

        $produk = $this->model('m_produk')->getProdukById($id);
        if (!$produk) {
            Flasher::setFlash('error', 'Produk tidak valid.');
            header('Location: ' . BASEURL . 'home');
            exit;
        }

        // Cek ketersediaan stok fisik di database
        if ($produk['stok'] < $qty) {
            Flasher::setFlash('error', 'Stok tidak mencukupi. Sisa stok: ' . $produk['stok']);
            header('Location: ' . BASEURL . 'home/detail/' . $id);
            exit;
        }

        // Struktur array item keranjang belanja
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Jika produk sudah ada di keranjang, akumulasikan jumlahnya (qty)
        if (isset($_SESSION['cart'][$id])) {
            $newQty = $_SESSION['cart'][$id]['qty'] + $qty;
            if ($newQty > $produk['stok']) {
                Flasher::setFlash('error', 'Jumlah di keranjang melebihi stok yang tersedia.');
                header('Location: ' . BASEURL . 'cart');
                exit;
            }
            $_SESSION['cart'][$id]['qty'] = $newQty;
            $_SESSION['cart'][$id]['subtotal'] = $newQty * $produk['harga'];
        } else {
            // Jika produk baru pertama kali dimasukkan ke keranjang
            $_SESSION['cart'][$id] = [
                'id'           => $produk['id'],
                'nama_produk'  => $produk['nama'],
                'icon'         => $produk['icon'],
                'satuan'       => $produk['satuan'],
                'harga'        => $produk['harga'],
                'qty'          => $qty,
                'subtotal'     => $produk['harga'] * $qty
            ];
        }

        Flasher::setFlash('success', 'Produk "' . $produk['nama'] . '" berhasil dimasukkan ke keranjang.');
        header('Location: ' . BASEURL . 'cart');
        exit;
    }

    // Menghapus satu baris item dari keranjang
    public function remove($id) {
        $id = (int)$id;
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            Flasher::setFlash('success', 'Item berhasil dihapus dari keranjang.');
        }
        header('Location: ' . BASEURL . 'cart');
        exit;
    }

    // Mengarahkan /cart/checkout ke route checkout utama
    public function checkout() {
        header('Location: ' . BASEURL . 'checkout');
        exit;
    }

    /**
     * Memproses data POST checkout dan menyimpannya ke database transaksi
     */
    public function prosesCheckout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_SESSION['cart']) || !isset($_SESSION['pelanggan_login'])) {
                header('Location: ' . BASEURL . 'home');
                exit;
            }

            // Validasi input data pengiriman dasar
            if (empty($_POST['alamat_kirim']) || empty($_POST['no_hp'])) {
                Flasher::setFlash('error', 'Alamat pengiriman dan Nomor HP wajib diisi!');
                header('Location: ' . BASEURL . 'cart/checkout');
                exit;
            }

            // Hitung kalkulasi total belanjaan dari session cart
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $item) {
                $subtotal += $item['subtotal'];
            }
            
            $ongkir = 15000; // Contoh flat rate ongkir (bisa di-nol-kan jika gratis)
            $diskon = 0;
            $total  = $subtotal + $ongkir - $diskon;

            // Generate nomor invoice acak unik
            $invoice = "INV-" . date('Ymd') . "-" . strtoupper(substr(md5(uniqid()), 0, 5));

            // Siapkan bundel data pesanan untuk dikirim ke model database
            $dataPesanan = [
                'pelanggan_id' => $_SESSION['pelanggan_id'],
                'invoice'      => $invoice,
                'metode_bayar' => $_POST['metode_bayar'],
                'alamat_kirim' => $_POST['alamat_kirim'],
                'kode_pos'     => $_POST['kode_pos'] ?? null,
                'no_hp'        => $_POST['no_hp'],
                'catatan'      => $_POST['catatan'] ?? null,
                'subtotal'     => $subtotal,
                'ongkir'       => $ongkir,
                'diskon'       => $diskon,
                'total'        => $total,
                'status'       => 'pending',
                'items'        => $_SESSION['cart'] // Kirim seluruh daftar item belanjaan
            ];

            // ⚠️ Proses penyimpanan ganda (Insert Pesanan & Detail Pesanan sekaligus potong stok)
            // Asumsi Anda menambahkan fungsi simpanPesananBaru() pada m_pesanan
            $db = getDB();
            try {
                $db->beginTransaction(); // Menggunakan Transaction PDO demi keamanan konsistensi data

                // 1. Insert ke tabel pesanan
                $stmt = $db->prepare("INSERT INTO pesanan (pelanggan_id, invoice, metode_bayar, alamat_kirim, kode_pos, no_hp, catatan, subtotal, ongkir, diskon, total, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$dataPesanan['pelanggan_id'], $dataPesanan['invoice'], $dataPesanan['metode_bayar'], $dataPesanan['alamat_kirim'], $dataPesanan['kode_pos'], $dataPesanan['no_hp'], $dataPesanan['catatan'], $dataPesanan['subtotal'], $dataPesanan['ongkir'], $dataPesanan['diskon'], $dataPesanan['total'], $dataPesanan['status']]);
                
                $pesananId = $db->lastInsertId();

                // 2. Loop detail items untuk insert detail_pesanan AND kurangi stok produk
                $stmtDetail = $db->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, satuan, harga, qty, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmtStok   = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");

                foreach ($dataPesanan['items'] as $item) {
                    // Cek ulang stok sebelum memotong untuk menghindari kecurangan/race condition
                    $pReal = $this->model('m_produk')->getProdukById($item['id']);
                    if ($pReal['stok'] < $item['qty']) {
                        throw new Exception("Stok untuk produk " . $item['nama_produk'] . " tiba-tiba habis!");
                    }

                    $stmtDetail->execute([$pesananId, $item['id'], $item['nama_produk'], $item['satuan'], $item['harga'], $item['qty'], $item['subtotal']]);
                    $stmtStok->execute([$item['qty'], $item['id']]);
                }

                $db->commit(); // Jika semua kueri sukses tanpa kendala, kunci perubahan database
                
                // Kosongkan keranjang belanja karena checkout sukses
                unset($_SESSION['cart']);

                Flasher::setFlash('success', 'Pesanan Anda berhasil dibuat! Nomor Invoice: ' . $invoice);
                header('Location: ' . BASEURL . 'checkout/success/' . urlencode($invoice));

            } catch (Exception $e) {
                $db->rollBack(); // Jika ada satu saja kueri yang gagal, batalkan semua transaksi
                Flasher::setFlash('error', 'Gagal memproses pesanan: ' . $e->getMessage());
                header('Location: ' . BASEURL . 'cart/checkout');
            }
            exit;
        }
    }
}