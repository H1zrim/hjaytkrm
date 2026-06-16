<?php

class m_pelanggan {
    private $table = 'pelanggan';
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    // ==========================================
    // ADMIN
    // ==========================================

    public function getAll($search = '') {
        $params = [];
        $where  = '';
        if (!empty($search)) {
            $where    = 'WHERE nama_pengguna LIKE ? OR email LIKE ? OR no_hp LIKE ?';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $stmt = $this->db->prepare("SELECT id, nama_pengguna, email, no_hp, created_at FROM {$this->table} $where ORDER BY created_at DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAllWithStats($search = '') {
        $params = [];
        $where  = '';
        if (!empty($search)) {
            $where    = 'WHERE pl.nama_pengguna LIKE ? OR pl.email LIKE ? OR pl.no_hp LIKE ?';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $stmt = $this->db->prepare(
            "SELECT pl.id, pl.nama_pengguna, pl.email, pl.no_hp, pl.nama_penerima, pl.created_at,
                    COUNT(p.id) AS total_pesanan,
                    COALESCE(SUM(CASE WHEN p.status = 'paid' THEN p.total ELSE 0 END), 0) AS total_belanja
             FROM {$this->table} pl
             LEFT JOIN pesanan p ON p.pelanggan_id = pl.id
             $where
             GROUP BY pl.id
             ORDER BY pl.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll() {
        return (int)$this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function getPelangganById($id) {
        $stmt = $this->db->prepare("SELECT id, nama_pengguna, email, no_hp, nama_penerima, alamat, kode_pos, created_at FROM {$this->table} WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public function hapusData($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->rowCount() > 0;
    }

    // ==========================================
    // AUTH & AKUN PELANGGAN
    // ==========================================

    public function register($data) {
        $hash  = password_hash($data['password'], PASSWORD_BCRYPT);
        $query = "INSERT INTO {$this->table}
                  (nama_pengguna, email, password, no_hp, nama_penerima, kode_pos, alamat, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            trim($data['nama_pengguna']),
            trim($data['email']),
            $hash,
            trim($data['no_hp'] ?? ''),
            trim($data['nama_penerima'] ?? ''),
            trim($data['kode_pos'] ?? ''),
            trim($data['alamat'] ?? ''),
        ]);
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([trim($email)]);
        $pelanggan = $stmt->fetch();
        if ($pelanggan && password_verify($password, $pelanggan['password'])) {
            unset($pelanggan['password']);
            return $pelanggan;
        }
        return false;
    }

    public function isEmailTerdaftar($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE email = ?");
        $stmt->execute([trim($email)]);
        return $stmt->fetchColumn() > 0;
    }

    public function updateProfil($id, $data) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nama_pengguna=?, no_hp=?, nama_penerima=?, alamat=?, kode_pos=? WHERE id=?");
        return $stmt->execute([
            trim($data['nama_pengguna'] ?? ''),
            trim($data['no_hp'] ?? ''),
            trim($data['nama_penerima'] ?? ''),
            trim($data['alamat'] ?? ''),
            trim($data['kode_pos'] ?? ''),
            (int)$id,
        ]);
    }

    public function updatePassword($id, $passwordBaru) {
        $hash = password_hash($passwordBaru, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password=? WHERE id=?");
        return $stmt->execute([$hash, (int)$id]);
    }
}
