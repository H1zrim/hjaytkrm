<?php

class m_admin {
    private $table = 'admin'; // Pastikan nama tabel di DB Anda benar
    private $db;

    public function __construct() {
        $this->db = getDB(); // Mengambil koneksi database
    }

    public function getAdminByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => trim($email)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}