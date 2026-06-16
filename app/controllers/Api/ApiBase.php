<?php

class ApiBase extends Controller {

    protected function json(array $payload, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function success($data = [], string $message = 'OK'): void {
        $this->json(['success' => true, 'message' => $message, 'data' => $data]);
    }

    protected function error(string $message, int $code = 400): void {
        $this->json(['success' => false, 'message' => $message, 'data' => null], $code);
    }

    /** Ambil Bearer token, validasi ke DB, return data pelanggan atau error 401 */
    protected function requireAuth(): array {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!$header || !preg_match('/Bearer\s+(\S+)/i', $header, $m)) {
            $this->error('Token tidak ditemukan. Sertakan header: Authorization: Bearer {token}', 401);
        }
        $token = $m[1];
        $stmt  = getDB()->prepare(
            "SELECT at.token, pl.id, pl.nama_pengguna, pl.email,
                    pl.no_hp, pl.nama_penerima, pl.alamat, pl.kode_pos
             FROM api_tokens at
             JOIN pelanggan pl ON pl.id = at.pelanggan_id
             WHERE at.token = ?
               AND (at.expired_at IS NULL OR at.expired_at > NOW())"
        );
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $this->error('Token tidak valid atau sudah expired. Silakan login ulang.', 401);
        }
        return $user;
    }

    /** Baca body JSON atau fallback ke $_POST */
    protected function getInput(): array {
        $raw  = file_get_contents('php://input');
        $json = $raw ? json_decode($raw, true) : null;
        return is_array($json) ? $json : $_POST;
    }
}
