<?php

class ApiPesanan extends ApiBase {

    /** GET /api/pesanan?status=&q=
     *  Status filter: pending | processed | paid | cancelled | all (default)
     */
    public function index(): void {
        $user   = $this->requireAuth();
        $status = isset($_GET['status']) ? trim($_GET['status']) : 'all';
        $search = isset($_GET['q'])      ? trim($_GET['q'])      : '';

        $model  = $this->model('m_pesanan');
        $list   = $model->getPesananByPelanggan($user['id'], $status, $search);

        foreach ($list as &$p) {
            $p['items'] = $model->getDetailItems((int)$p['id']);
        }
        unset($p);

        $this->success([
            'total'   => count($list),
            'pesanan' => $list,
        ]);
    }

    /** GET /api/pesanan/detail/{id} */
    public function detail(int $id = 0): void {
        $user = $this->requireAuth();

        if ($id <= 0) {
            $this->error('ID pesanan tidak valid.');
        }

        $model   = $this->model('m_pesanan');
        $pesanan = $model->getById($id);

        if (!$pesanan) {
            $this->error('Pesanan tidak ditemukan.', 404);
        }
        if ((int)$pesanan['pelanggan_id'] !== (int)$user['id']) {
            $this->error('Anda tidak memiliki akses ke pesanan ini.', 403);
        }

        $pesanan['items'] = $model->getDetailItems($id);

        $this->success($pesanan);
    }
}
