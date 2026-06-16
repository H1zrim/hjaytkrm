<?php

class m_kategori {
    private $table = 'kategori';
    private $db;

    public function __construct() {
        // Mengambil koneksi database global PDO dari config Anda
        $this->db = getDB();
    }

    /**
     * Mengambil semua daftar kategori
     * Biasanya digunakan untuk dropdown select di form produk, stok, atau filter katalog client
     */
    public function getAllKategori() {
        $query = "
            SELECT k.*, COALESCE(p.jml_produk, 0) AS jml_produk
            FROM " . $this->table . " k
            LEFT JOIN (
                SELECT kategori_id, COUNT(*) AS jml_produk
                FROM produk
                GROUP BY kategori_id
            ) p ON p.kategori_id = k.id
            ORDER BY k.nama_kategori ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        return $this->getAllKategori();
    }

    /**
     * Mengambil satu data kategori berdasarkan ID
     */
    public function getKategoriById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menambah kategori produk baru
     */
    public function tambahKategori($data) {
        $query = "INSERT INTO " . $this->table . " (icon, nama_kategori, deskripsi) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            trim($data['icon'] ?? ''),
            trim($data['nama_kategori'] ?? ''),
            trim($data['deskripsi'] ?? '')
        ]);
    }

    public function tambahData($data) {
        return $this->tambahKategori($data);
    }

    /**
     * Memperbarui data kategori
     */
    public function updateKategori($data) {
        $query = "UPDATE " . $this->table . " SET icon = ?, nama_kategori = ?, deskripsi = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            trim($data['icon'] ?? ''),
            trim($data['nama_kategori'] ?? ''),
            trim($data['deskripsi'] ?? ''),
            (int)$data['id']
        ]);
    }

    public function updateData($data) {
        return $this->updateKategori($data);
    }

    /**
     * Menghapus kategori produk
     * Catatan Keamanan: Sebaiknya divalidasi dulu di Controller apakah ada produk yang masih memakai kategori ini
     */
    public function hapusKategori($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([(int)$id]);
    }

    public function hapusData($id) {
        return $this->hapusKategori($id);
    }
}