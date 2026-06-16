<?php

class ApiCart extends ApiBase {

    /** GET /api/cart  — isi keranjang + total harga */
    public function index(): void {
        $user  = $this->requireAuth();
        $items = $this->getCartItems($user['id']);

        $subtotal = array_sum(array_column($items, 'subtotal'));
        $ongkir   = ($subtotal > 0 && $subtotal >= 150000) ? 0 : 15000;
        $total    = $subtotal + $ongkir;

        $this->success([
            'items'    => $items,
            'subtotal' => $subtotal,
            'ongkir'   => $ongkir,
            'total'    => $total,
            'gratis_ongkir_min' => 150000,
        ]);
    }

    /** POST /api/cart/add
     *  Body: { "produk_id": 1, "qty": 2 }
     */
    public function add(): void {
        $user  = $this->requireAuth();
        $input = $this->getInput();

        $produkId = (int)($input['produk_id'] ?? 0);
        $qty      = max(1, (int)($input['qty'] ?? 1));

        if ($produkId <= 0) {
            $this->error('produk_id wajib diisi.');
        }

        $produk = $this->model('m_produk')->getProdukById($produkId);
        if (!$produk) {
            $this->error('Produk tidak ditemukan.', 404);
        }

        // Cek stok vs qty yang sudah di cart
        $db      = getDB();
        $existing = $db->prepare("SELECT qty FROM api_cart WHERE pelanggan_id = ? AND produk_id = ?");
        $existing->execute([$user['id'], $produkId]);
        $cartQty = (int)($existing->fetchColumn() ?: 0);
        $newQty  = $cartQty + $qty;

        if ($newQty > $produk['stok']) {
            $this->error("Stok tidak cukup. Stok tersedia: {$produk['stok']}, sudah di cart: {$cartQty}.");
        }

        $db->prepare(
            "INSERT INTO api_cart (pelanggan_id, produk_id, qty)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)"
        )->execute([$user['id'], $produkId, $qty]);

        $this->success([], "\"{$produk['nama']}\" ditambahkan ke keranjang.");
    }

    /** POST /api/cart/update
     *  Body: { "produk_id": 1, "qty": 3 }
     */
    public function update(): void {
        $user  = $this->requireAuth();
        $input = $this->getInput();

        $produkId = (int)($input['produk_id'] ?? 0);
        $qty      = (int)($input['qty'] ?? 0);

        if ($produkId <= 0 || $qty <= 0) {
            $this->error('produk_id dan qty (> 0) wajib diisi.');
        }

        $produk = $this->model('m_produk')->getProdukById($produkId);
        if (!$produk) {
            $this->error('Produk tidak ditemukan.', 404);
        }
        if ($qty > $produk['stok']) {
            $this->error("Qty melebihi stok yang tersedia ({$produk['stok']}).");
        }

        getDB()->prepare(
            "UPDATE api_cart SET qty = ? WHERE pelanggan_id = ? AND produk_id = ?"
        )->execute([$qty, $user['id'], $produkId]);

        $this->success([], 'Jumlah produk diperbarui.');
    }

    /** POST /api/cart/remove
     *  Body: { "produk_id": 1 }
     */
    public function remove(): void {
        $user     = $this->requireAuth();
        $input    = $this->getInput();
        $produkId = (int)($input['produk_id'] ?? 0);

        if ($produkId <= 0) {
            $this->error('produk_id wajib diisi.');
        }

        getDB()->prepare(
            "DELETE FROM api_cart WHERE pelanggan_id = ? AND produk_id = ?"
        )->execute([$user['id'], $produkId]);

        $this->success([], 'Produk dihapus dari keranjang.');
    }

    /** POST /api/cart/clear */
    public function clear(): void {
        $user = $this->requireAuth();
        getDB()->prepare("DELETE FROM api_cart WHERE pelanggan_id = ?")->execute([$user['id']]);
        $this->success([], 'Keranjang dikosongkan.');
    }

    // -------------------------------------------------------

    private function getCartItems(int $pelangganId): array {
        $stmt = getDB()->prepare(
            "SELECT ac.produk_id, ac.qty,
                    pr.nama, pr.icon, pr.foto, pr.harga, pr.satuan, pr.stok,
                    (ac.qty * pr.harga) AS subtotal
             FROM api_cart ac
             JOIN produk pr ON pr.id = ac.produk_id
             WHERE ac.pelanggan_id = ?"
        );
        $stmt->execute([$pelangganId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['foto_url'] = !empty($r['foto']) ? BASEURL . 'uploads/produk/' . $r['foto'] : null;
        }
        unset($r);

        return $rows;
    }
}
