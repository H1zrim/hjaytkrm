<?php

class m_stok {
    private $table = 'produk';
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    // Mengambil daftar stok dengan pengurutan dari yang paling sedikit
    public function getStokFiltered($search = '', $katFilter = 0, $showLow = false) {
        $params = [];
        $where  = [];

        if ($search) { 
            $where[] = "pr.nama LIKE ?"; 
            $params[] = "%$search%"; 
        }
        if ($katFilter) { 
            $where[] = "pr.kategori_id = ?"; 
            $params[] = (int)$katFilter; 
        }
        if ($showLow) { 
            $where[] = "pr.stok <= 10"; 
        }

        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT pr.*, k.nama_kategori 
                  FROM " . $this->table . " pr 
                  LEFT JOIN kategori k ON k.id = pr.kategori_id 
                  $whereStr 
                  ORDER BY pr.stok ASC, pr.nama ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menghitung jumlah produk yang stoknya <= 10
    public function countStokRendah() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM " . $this->table . " WHERE stok <= 10");
        return (int)$stmt->fetchColumn();
    }

    // Memperbarui kuantitas stok langsung berdasarkan ID produk
    public function updateJumlahStok($id, $stokBaru) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET stok = ? WHERE id = ?");
        return $stmt->execute([max(0, $stokBaru), (int)$id]);
    }
}