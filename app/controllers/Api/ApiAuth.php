<?php

class ApiAuth extends ApiBase {

    /** POST /api/auth/login
     *  Body: { "email": "...", "password": "..." }
     *  Returns: { token, user }
     */
    public function login(): void {
        $input    = $this->getInput();
        $email    = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (!$email || !$password) {
            $this->error('Email dan password wajib diisi.');
        }

        $pelanggan = $this->model('m_pelanggan')->login($email, $password);
        if (!$pelanggan) {
            $this->error('Email atau password salah.', 401);
        }

        $db    = getDB();
        $token = bin2hex(random_bytes(32));

        // Hapus token lama, simpan token baru (berlaku 30 hari)
        $db->prepare("DELETE FROM api_tokens WHERE pelanggan_id = ?")->execute([$pelanggan['id']]);
        $db->prepare("INSERT INTO api_tokens (pelanggan_id, token, expired_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))")
           ->execute([$pelanggan['id'], $token]);

        $this->success([
            'token' => $token,
            'user'  => $pelanggan,
        ], 'Login berhasil');
    }

    /** POST /api/auth/register
     *  Body: { "nama_pengguna", "email", "password", "no_hp" }
     */
    public function register(): void {
        $input        = $this->getInput();
        $nama         = trim($input['nama_pengguna'] ?? '');
        $email        = trim($input['email'] ?? '');
        $password     = $input['password'] ?? '';
        $no_hp        = trim($input['no_hp'] ?? '');

        if (!$nama || !$email || !$password) {
            $this->error('Nama, email, dan password wajib diisi.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Format email tidak valid.');
        }
        if (strlen($password) < 6) {
            $this->error('Password minimal 6 karakter.');
        }

        $m = $this->model('m_pelanggan');
        if ($m->isEmailTerdaftar($email)) {
            $this->error('Email sudah terdaftar.', 409);
        }

        $ok = $m->register([
            'nama_pengguna' => $nama,
            'email'         => $email,
            'password'      => $password,
            'no_hp'         => $no_hp,
            'nama_penerima' => $nama,
            'kode_pos'      => '',
            'alamat'        => '',
        ]);

        if (!$ok) {
            $this->error('Gagal membuat akun.', 500);
        }

        $this->success([], 'Registrasi berhasil. Silakan login.', );
    }

    /** POST /api/auth/logout  (Protected)
     *  Header: Authorization: Bearer {token}
     */
    public function logout(): void {
        $this->requireAuth();

        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        preg_match('/Bearer\s+(\S+)/i', $header, $m);
        getDB()->prepare("DELETE FROM api_tokens WHERE token = ?")->execute([$m[1]]);

        $this->success([], 'Logout berhasil. Token dihapus.');
    }

    /** GET /api/auth/me  (Protected) — cek token & return user info */
    public function me(): void {
        $user = $this->requireAuth();
        unset($user['token']);
        $this->success($user);
    }
}
