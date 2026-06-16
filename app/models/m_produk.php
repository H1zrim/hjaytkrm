<?php

class m_produk {
    private $table = 'produk';
    private $db;

    public function __construct() {
        // Mengambil instance database koneksi PDO dari core/global sistem Anda
        $this->db = getDB(); 
    }

    // Mengambil semua produk dengan filter pencarian & kategori
    public function getProdukFiltered($search = '', $katFilter = 0) {
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
        
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT pr.*, k.nama_kategori 
                  FROM " . $this->table . " pr 
                  LEFT JOIN kategori k ON k.id = pr.kategori_id 
                  $whereStr 
                  ORDER BY pr.id DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil satu data produk berdasarkan ID
    public function getProdukById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        return $this->getProdukFiltered();
    }

    public function getById($id) {
        return $this->getProdukById($id);
    }

    // Tambah Produk Baru
    public function tambahProduk($data) {
        $query = "INSERT INTO " . $this->table . " (kategori_id, nama, deskripsi, harga, stok, satuan, icon, badge) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            (int)$data['kategori_id'],
            trim($data['nama']),
            trim($data['deskripsi']),
            (float)$data['harga'],
            max(0, (int)$data['stok']),
            trim($data['satuan']),
            trim($data['icon']),
            trim($data['badge'])
        ]);
    }

    public function tambahData($data) {
        return $this->tambahProduk($data);
    }

    // Update Data Produk
    public function updateProduk($data) {
        $query = "UPDATE " . $this->table . " 
                  SET kategori_id = ?, nama = ?, deskripsi = ?, harga = ?, stok = ?, satuan = ?, icon = ?, badge = ? 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            (int)$data['kategori_id'],
            trim($data['nama']),
            trim($data['deskripsi']),
            (float)$data['harga'],
            max(0, (int)$data['stok']),
            trim($data['satuan']),
            trim($data['icon']),
            trim($data['badge']),
            (int)$data['id']
        ]);
    }

    public function updateData($data) {
        return $this->updateProduk($data);
    }

    public function updateStok($data) {
        $query = "UPDATE produk SET stok = :stok WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'stok' => max(0, (int)$data['stok']),
            'id' => (int)$data['id']
        ]);
        return $stmt->rowCount();
    }

    public function hapusData($id) {
        return $this->hapusProduk($id);
    }

    // Cek apakah produk sudah pernah dipesan (Validasi sebelum hapus)
    public function cekRiwayatPesanan($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM detail_pesanan WHERE produk_id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetchColumn() > 0;
    }

    // Hapus Produk
    public function hapusProduk($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}