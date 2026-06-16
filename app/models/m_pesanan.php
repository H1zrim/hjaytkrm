<?php

class m_pesanan {
    private $table = 'pesanan';
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    // Mengambil daftar pesanan dengan filter status & pencarian invoice/nama
    public function getPesananFiltered($status = '', $search = '') {
        $params = [];
        $where  = [];

        if ($status) { 
            $where[] = "p.status = ?"; 
            $params[] = $status; 
        }
        if ($search) { 
            $where[] = "(p.invoice LIKE ? OR pl.nama_pengguna LIKE ? OR pl.email LIKE ?)"; 
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT p.*, pl.nama_pengguna, pl.email 
                  FROM " . $this->table . " p 
                  JOIN pelanggan pl ON pl.id = p.pelanggan_id 
                  $whereStr 
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        return $this->getPesananFiltered();
    }

    public function getById($id) {
        return $this->getPesananById($id);
    }

    public function getItemByPesananId($id) {
        return $this->getDetailItems($id);
    }

    public function getPesananByStatus($status) {
        return $this->getPesananFiltered($status, '');
    }

    public function getTotalOmzet() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total), 0) FROM " . $this->table . " WHERE status = 'paid'");
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    public function getByInvoice($invoice) {
        $query = "SELECT p.*, pl.nama_pengguna, pl.email, pl.no_hp as pelanggan_hp 
                  FROM " . $this->table . " p 
                  JOIN pelanggan pl ON pl.id = p.pelanggan_id 
                  WHERE p.invoice = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([trim($invoice)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPesananByPelanggan($pelangganId, $status = 'all', $search = '') {
        $params = [(int)$pelangganId];
        $where = ["p.pelanggan_id = ?"];

        if ($status && $status !== 'all') {
            $where[] = "p.status = ?";
            $params[] = $status;
        }
        if ($search) {
            $where[] = "(p.invoice LIKE ? OR p.metode_bayar LIKE ? OR p.alamat_kirim LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $query = "SELECT p.* FROM " . $this->table . " p $whereStr ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatusCountsByPelanggan($pelangganId) {
        $stmt = $this->db->prepare("SELECT status, COUNT(*) as n FROM " . $this->table . " WHERE pelanggan_id = ? GROUP BY status");
        $stmt->execute([(int)$pelangganId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Mengambil data pesanan spesifik beserta informasi pelanggan untuk Modal Detail
    public function getPesananById($id) {
        $query = "SELECT p.*, pl.nama_pengguna, pl.email, pl.no_hp as pelanggan_hp 
                  FROM " . $this->table . " p 
                  JOIN pelanggan pl ON pl.id = p.pelanggan_id 
                  WHERE p.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mengambil item produk di dalam pesanan terkait
    public function getDetailItems($pesananId) {
        $query = "SELECT dp.*, pr.icon 
                  FROM detail_pesanan dp 
                  LEFT JOIN produk pr ON pr.id = dp.produk_id 
                  WHERE dp.pesanan_id = ?";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute([(int)$pesananId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil jumlah total pesanan dikelompokkan berdasarkan status untuk angka di Tab Menu
    public function getStatusCounts() {
        $query = "SELECT status, COUNT(*) as n FROM " . $this->table . " GROUP BY status";
        return $this->db->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Mengubah status pesanan (Konfirmasi / Selesai / Dibatalkan)
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, (int)$id]);
    }

    public function updateBuktiBayar($id, $buktiPath) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET bukti_bayar = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([trim($buktiPath), (int)$id]);
    }

    // Mengembalikan stok produk ketika pesanan dibatalkan (Cancel Order)
    public function kembalikanStokPesanan($pesananId) {
        // 1. Ambil semua item dari pesanan tersebut
        $items = $this->getDetailItems($pesananId);
        
        // 2. Kembalikan stok masing-masing produk lewat perulangan
        $queryUpdate = "UPDATE produk SET stok = stok + ? WHERE id = ?";
        $stmtUpdate = $this->db->prepare($queryUpdate);
        
        foreach ($items as $item) {
            $stmtUpdate->execute([(int)$item['qty'], (int)$item['produk_id']]);
        }
        return true;
    }

    public function batalkanPesanan($pesananId) {
        try {
            $this->db->beginTransaction();

            // 1. Ambil data pesanan untuk cek status
            $pesanan = $this->getPesananById($pesananId);
            
            // Cek jika status bukan 'cancelled', baru kembalikan stok
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