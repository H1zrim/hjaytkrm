<?php

class m_pelanggan {
    private $table = 'pelanggan';
    private $db;

    public function __construct() {
        // Mengambil koneksi database global PDO Anda
        $this->db = getDB();
    }

    // ==========================================
    // 1. FUNGSI UNTUK SISI ADMIN (MANAJEMEN)
    // ==========================================

    /**
     * Mengambil semua data pelanggan dengan fitur pencarian nama atau email
     */
    public function getAllPelangganFiltered($search = '') {
        $params = [];
        $whereClause = '';

        if (!empty($search)) {
            $whereClause = "WHERE nama_pengguna LIKE ? OR email LIKE ? OR no_hp LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query = "SELECT id, nama_pengguna, email, no_hp, created_at 
                  FROM " . $this->table . " 
                  $whereClause 
                  ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil detail profil pelanggan berdasarkan ID (untuk detail transaksi / profil)
     */
    public function getAll($search = '') {
        return $this->getAllPelangganFiltered($search);
    }

    public function getPelangganById($id) {
        $stmt = $this->db->prepare("SELECT id, nama_pengguna, email, no_hp, nama_penerima, alamat, kode_pos, kota, created_at FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // ==========================================
    // 2. FUNGSI AUTENTIKASI & AKUN (SISI CLIENT)
    // ==========================================

    /**
     * Menangani proses registrasi akun pelanggan baru
     */
    public function register($data) {
    $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

    // Pastikan kolom-kolom ini ada di tabel 'pelanggan' Anda
    $query = "INSERT INTO " . $this->table . " 
              (nama_pengguna, email, password, no_hp, nama_penerima, kode_pos, alamat, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $this->db->prepare($query);
    return $stmt->execute([
        trim($data['nama_pengguna']),
        trim($data['email']),
        $passwordHash,
        trim($data['no_hp'] ?? ''),
        trim($data['nama_penerima'] ?? ''),
        trim($data['kode_pos'] ?? ''),
        trim($data['alamat'] ?? '')
    ]);
}

    /**
     * Menangani proses login pelanggan
     * Mengembalikan data user jika sukses, atau false jika email/password salah
     */
    public function login($email, $password) {
        // 1. Cari data pelanggan berdasarkan email
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE email = ?");
        $stmt->execute([trim($email)]);
        $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Jika akun ditemukan, verifikasi password hash-nya
        if ($pelanggan && password_verify($password, $pelanggan['password'])) {
            // Hapus index password dari array sebelum dikembalikan ke controller demi keamanan session
            unset($pelanggan['password']);
            return $pelanggan;
        }

        return false;
    }

    /**
     * Memeriksa apakah suatu email sudah terdaftar di sistem (untuk validasi register)
     */
    public function isEmailTerdaftar($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM " . $this->table . " WHERE email = ?");
        $stmt->execute([trim($email)]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Memperbarui informasi profil pelanggan (Nama, No HP, alamat, dsb.)
     */
    public function updateProfil($id, $data) {
        $query = "UPDATE " . $this->table . " SET nama_pengguna = ?, no_hp = ?, nama_penerima = ?, alamat = ?, kode_pos = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            trim($data['nama_pengguna'] ?? ''),
            trim($data['no_hp'] ?? ''),
            trim($data['nama_penerima'] ?? ''),
            trim($data['alamat'] ?? ''),
            trim($data['kode_pos'] ?? ''),
            (int)$id
        ]);
    }

    /**
     * Mengubah password pelanggan (dengan memverifikasi password lama terlebih dahulu di controller)
     */
    public function updatePassword($id, $passwordBaru) {
        $passwordHash = password_hash($passwordBaru, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET password = ? WHERE id = ?");
        return $stmt->execute([$passwordHash, (int)$id]);
    }
}