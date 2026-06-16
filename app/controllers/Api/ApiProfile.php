<?php

class ApiProfile extends ApiBase {

    /** GET /api/profile */
    public function index(): void {
        $user    = $this->requireAuth();
        $profil  = $this->model('m_pelanggan')->getPelangganById($user['id']);
        $this->success($profil);
    }

    /** POST /api/profile/update
     *  Body: { "nama_pengguna", "no_hp", "nama_penerima", "alamat", "kode_pos" }
     */
    public function update(): void {
        $user  = $this->requireAuth();
        $input = $this->getInput();

        $nama = trim($input['nama_pengguna'] ?? '');
        if (!$nama) {
            $this->error('nama_pengguna wajib diisi.');
        }

        $ok = $this->model('m_pelanggan')->updateProfil($user['id'], [
            'nama_pengguna' => $nama,
            'no_hp'         => trim($input['no_hp'] ?? ''),
            'nama_penerima' => trim($input['nama_penerima'] ?? $nama),
            'alamat'        => trim($input['alamat'] ?? ''),
            'kode_pos'      => trim($input['kode_pos'] ?? ''),
        ]);

        if (!$ok) {
            $this->error('Gagal memperbarui profil.', 500);
        }

        $this->success([], 'Profil berhasil diperbarui.');
    }

    /** POST /api/profile/ganti-password
     *  Body: { "password_lama", "password_baru" }
     */
    public function gantiPassword(): void {
        $user  = $this->requireAuth();
        $input = $this->getInput();

        $lama = $input['password_lama'] ?? '';
        $baru = $input['password_baru'] ?? '';

        if (!$lama || !$baru) {
            $this->error('password_lama dan password_baru wajib diisi.');
        }
        if (strlen($baru) < 6) {
            $this->error('Password baru minimal 6 karakter.');
        }

        // Verifikasi password lama
        $stmt = getDB()->prepare("SELECT password FROM pelanggan WHERE id = ?");
        $stmt->execute([$user['id']]);
        $hash = $stmt->fetchColumn();

        if (!password_verify($lama, $hash)) {
            $this->error('Password lama tidak sesuai.', 401);
        }

        $this->model('m_pelanggan')->updatePassword($user['id'], $baru);
        $this->success([], 'Password berhasil diubah.');
    }
}
