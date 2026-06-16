<?php

class ApiCheckout extends ApiBase {

    /** POST /api/checkout/proses
     *  Body: {
     *    "nama_penerima", "no_hp", "alamat",
     *    "kode_pos" (opsional), "catatan" (opsional),
     *    "metode_bayar": "qris"|"transfer"|"cod"
     *  }
     */
    public function proses(): void {
        $user  = $this->requireAuth();
        $input = $this->getInput();

        // Ambil cart dari DB
        $stmt = getDB()->prepare(
            "SELECT ac.produk_id, ac.qty, pr.nama, pr.icon, pr.foto,
                    pr.harga, pr.satuan, pr.stok,
                    (ac.qty * pr.harga) AS subtotal
             FROM api_cart ac
             JOIN produk pr ON pr.id = ac.produk_id
             WHERE ac.pelanggan_id = ?"
        );
        $stmt->execute([$user['id']]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            $this->error('Keranjang kosong. Tambahkan produk terlebih dahulu.');
        }

        // Validasi field wajib
        $alamat      = trim($input['alamat'] ?? '');
        $no_hp       = trim($input['no_hp'] ?? '');
        $nama_penerima = trim($input['nama_penerima'] ?? $user['nama_penerima'] ?? '');
        $metode      = trim($input['metode_bayar'] ?? 'transfer');

        if (!$alamat || !$no_hp) {
            $this->error('Alamat dan no_hp wajib diisi.');
        }
        if (!in_array($metode, ['qris', 'transfer', 'cod'], true)) {
            $this->error("metode_bayar harus salah satu dari: qris, transfer, cod.");
        }

        $subtotal = array_sum(array_column($cartItems, 'subtotal'));
        $ongkir   = $subtotal >= 150000 ? 0 : 15000;
        $total    = $subtotal + $ongkir;
        $invoice  = 'INV-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $db = getDB();
        try {
            $db->beginTransaction();

            // Insert pesanan
            $db->prepare(
                "INSERT INTO pesanan (pelanggan_id, invoice, metode_bayar, nama_penerima,
                            no_hp, alamat_kirim, kode_pos, catatan,
                            subtotal, ongkir, diskon, total, status, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 'pending', NOW())"
            )->execute([
                $user['id'], $invoice, $metode, $nama_penerima,
                $no_hp, $alamat,
                trim($input['kode_pos'] ?? ''),
                trim($input['catatan'] ?? ''),
                $subtotal, $ongkir, $total,
            ]);

            $pesananId  = $db->lastInsertId();
            $stmtDetail = $db->prepare(
                "INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, satuan, harga, qty, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmtStok = $db->prepare("UPDATE produk SET stok = GREATEST(0, stok - ?) WHERE id = ?");

            foreach ($cartItems as $item) {
                // Re-check stok saat transaksi
                $realStok = (int)$db->query("SELECT stok FROM produk WHERE id = {$item['produk_id']}")->fetchColumn();
                if ($realStok < $item['qty']) {
                    throw new Exception("Stok \"{$item['nama']}\" tidak mencukupi (sisa {$realStok}).");
                }
                $stmtDetail->execute([
                    $pesananId, $item['produk_id'], $item['nama'],
                    $item['satuan'], $item['harga'], $item['qty'], $item['subtotal'],
                ]);
                $stmtStok->execute([$item['qty'], $item['produk_id']]);
            }

            // Kosongkan api_cart
            $db->prepare("DELETE FROM api_cart WHERE pelanggan_id = ?")->execute([$user['id']]);

            $db->commit();

            $this->success([
                'invoice'  => $invoice,
                'total'    => $total,
                'ongkir'   => $ongkir,
                'subtotal' => $subtotal,
                'status'   => 'pending',
            ], "Pesanan berhasil dibuat! Invoice: $invoice");

        } catch (Exception $e) {
            $db->rollBack();
            $this->error('Gagal memproses pesanan: ' . $e->getMessage(), 500);
        }
    }
}
