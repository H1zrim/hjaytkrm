<?php

class m_pesanan {
    private $table = 'pesanan';
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    // ==========================================
    // ADMIN — Daftar & Statistik
    // ==========================================

    public function getAll() {
        return $this->getPesananFiltered();
    }

    public function getPesananFiltered($status = '', $search = '') {
        $params = [];
        $where  = [];
        if ($status) { $where[] = 'p.status = ?'; $params[] = $status; }
        if ($search) {
            $where[]  = '(p.invoice LIKE ? OR pl.nama_pengguna LIKE ? OR pl.email LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $w = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->db->prepare(
            "SELECT p.*, pl.nama_pengguna, pl.email
             FROM {$this->table} p
             JOIN pelanggan pl ON pl.id = p.pelanggan_id
             $w
             ORDER BY p.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll() {
        return (int)$this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function countByStatus($status) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE status = ?");
        $stmt->execute([$status]);
        return (int)$stmt->fetchColumn();
    }

    public function getTotalOmzet() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total), 0) FROM {$this->table} WHERE status = 'paid'");
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    public function getStatusCounts() {
        return $this->db->query("SELECT status, COUNT(*) as n FROM {$this->table} GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function getPesananByStatus($status) {
        return $this->getPesananFiltered($status);
    }

    // ==========================================
    // DETAIL PESANAN
    // ==========================================

    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT p.*, pl.nama_pengguna, pl.email, pl.no_hp as pelanggan_hp
             FROM {$this->table} p
             JOIN pelanggan pl ON pl.id = p.pelanggan_id
             WHERE p.id = ?"
        );
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public function getItemByPesananId($id) {
        return $this->getDetailItems($id);
    }

    public function getDetailItems($pesananId) {
        $stmt = $this->db->prepare(
            "SELECT dp.*, pr.icon, pr.foto
             FROM detail_pesanan dp
             LEFT JOIN produk pr ON pr.id = dp.produk_id
             WHERE dp.pesanan_id = ?"
        );
        $stmt->execute([(int)$pesananId]);
        return $stmt->fetchAll();
    }

    public function getByInvoice($invoice) {
        $stmt = $this->db->prepare(
            "SELECT p.*, pl.nama_pengguna, pl.email, pl.no_hp as pelanggan_hp
             FROM {$this->table} p
             JOIN pelanggan pl ON pl.id = p.pelanggan_id
             WHERE p.invoice = ?"
        );
        $stmt->execute([trim($invoice)]);
        return $stmt->fetch();
    }

    // ==========================================
    // PELANGGAN — Riwayat Pesanan
    // ==========================================

    public function getPesananByPelanggan($pelangganId, $status = 'all', $search = '') {
        $params = [(int)$pelangganId];
        $where  = ['p.pelanggan_id = ?'];
        if ($status && $status !== 'all') { $where[] = 'p.status = ?'; $params[] = $status; }
        if ($search) {
            $where[]  = '(p.invoice LIKE ? OR p.metode_bayar LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $w    = 'WHERE ' . implode(' AND ', $where);
        $stmt = $this->db->prepare("SELECT p.* FROM {$this->table} p $w ORDER BY p.created_at DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStatusCountsByPelanggan($pelangganId) {
        $stmt = $this->db->prepare("SELECT status, COUNT(*) as n FROM {$this->table} WHERE pelanggan_id = ? GROUP BY status");
        $stmt->execute([(int)$pelangganId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // ==========================================
    // UPDATE
    // ==========================================

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status=?, updated_at=NOW() WHERE id=?");
        return $stmt->execute([$status, (int)$id]);
    }

    public function updateBuktiBayar($id, $buktiPath) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET bukti_bayar=?, status='processed', updated_at=NOW() WHERE id=?");
        return $stmt->execute([trim($buktiPath), (int)$id]);
    }

    public function kembalikanStokPesanan($pesananId) {
        $items       = $this->getDetailItems($pesananId);
        $stmtUpdate  = $this->db->prepare("UPDATE produk SET stok = stok + ? WHERE id = ?");
        foreach ($items as $item) {
            $stmtUpdate->execute([(int)$item['qty'], (int)$item['produk_id']]);
        }
        return true;
    }

    public function batalkanPesanan($pesananId) {
        try {
            $this->db->beginTransaction();
            $pesanan = $this->getById($pesananId);
            if ($pesanan && $pesanan['status'] !== 'cancelled') {
                $this->kembalikanStokPesanan($pesananId);
                $this->updateStatus($pesananId, 'cancelled');
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
