<?php

class ApiKategori extends ApiBase {

    /** GET /api/kategori */
    public function index(): void {
        $list = $this->model('m_kategori')->getAll();
        $this->success(['total' => count($list), 'kategori' => $list]);
    }
}
