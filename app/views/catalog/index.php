<?php
$this->view('/layouts/header-pelanggan', $data);
?>

<div class="page-header">
  <div class="breadcrumb"><a href="<?= BASEURL; ?>">Beranda</a> › Katalog Produk</div>
  <h1>Katalog Produk</h1>
  <p>Temukan berbagai produk kurma dan oleh-oleh haji pilihan terbaik</p>
</div>

<div style="background:var(--white-warm);border-bottom:1px solid var(--border-sand);padding:14px 32px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
  <form method="GET" action="<?= BASEURL; ?>catalog" style="display:flex;align-items:center;gap:10px;flex:1;flex-wrap:wrap;">
    
    <div style="position:relative;flex:1;min-width:180px;max-width:380px;">
      <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:15px;color:var(--text-light);">🔍</span>
      <input type="text" name="q" class="form-control" style="padding-left:38px;"
             placeholder="Cari produk..." value="<?= htmlspecialchars($data['search'] ?? '') ?>">
    </div>
    
    <select name="kat" class="form-control" style="width:auto;" onchange="this.form.submit()">
      <option value="">Semua Kategori</option>
      <?php foreach ($data['kategoriAll'] as $k): ?>
        <option value="<?= $k['id'] ?>" <?= (isset($data['katFilter']) && $data['katFilter'] == $k['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($k['icon'] . ' ' . $k['nama_kategori']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    
    <select name="sort" class="form-control" style="width:auto;" onchange="this.form.submit()">
      <option value="default" <?= (isset($data['sort']) && $data['sort'] === 'default') ? 'selected' : '' ?>>Urutkan: Default</option>
      <option value="price_asc" <?= (isset($data['sort']) && $data['sort'] === 'price_asc') ? 'selected' : '' ?>>Harga Terendah</option>
      <option value="price_desc" <?= (isset($data['sort']) && $data['sort'] === 'price_desc') ? 'selected' : '' ?>>Harga Tertinggi</option>
      <option value="name" <?= (isset($data['sort']) && $data['sort'] === 'name') ? 'selected' : '' ?>>Nama A–Z</option>
    </select>
    
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    
    <?php if (!empty($data['search']) || !empty($data['katFilter']) || (isset($data['sort']) && $data['sort'] !== 'default')): ?>
      <a href="<?= BASEURL; ?>catalog" class="btn btn-outline btn-sm">✕ Reset</a>
    <?php endif; ?>
  </form>
</div>

<div class="section" style="padding-top:28px;">
  <div style="margin-bottom:14px;font-size:13px;color:var(--text-light);">
    <?php if (!empty($data['search']) || !empty($data['katFilter'])): ?>
      Menampilkan <strong><?= count($data['produkList']) ?></strong> produk
      <?= !empty($data['search']) ? 'untuk "<strong>' . htmlspecialchars($data['search']) . '</strong>"' : '' ?>
    <?php else: ?>
      Total <strong><?= count($data['produkList']) ?></strong> produk tersedia
    <?php endif; ?>
  </div>

  <?php if (empty($data['produkList'])): ?>
    <div class="empty-state">
      <div class="ei">🔍</div>
      <h3>Produk tidak ditemukan</h3>
      <p>Coba kata kunci lain atau hapus filter yang aktif.</p>
      <a href="<?= BASEURL; ?>catalog" class="btn btn-primary">Lihat Semua Produk</a>
    </div>
  <?php else: ?>
    <div class="products-grid">
      <?php foreach ($data['produkList'] as $p): ?>
        <a href="<?= BASEURL; ?>catalog/detail/<?= $p['id'] ?>" class="product-card" style="text-decoration:none;">
          <div class="prod-img">
            <?php if (!empty($p['foto'])): ?>
              <img src="<?= BASEURL ?>uploads/produk/<?= htmlspecialchars($p['foto']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
            <?php else: ?>
              <?= htmlspecialchars($p['icon']) ?>
            <?php endif; ?>
            <?php if ($p['badge']): ?><div class="prod-badge"><?= htmlspecialchars($p['badge']) ?></div><?php endif; ?>
            <?php if ($p['stok'] == 0): ?>
              <div style="position:absolute;inset:0;background:rgba(42,29,10,.5);display:flex;align-items:center;justify-content:center;color:white;font-size:13px;font-weight:700;border-radius:0;">Stok Habis</div>
            <?php endif; ?>
          </div>
          <div class="prod-body">
            <div class="prod-cat"><?= htmlspecialchars($p['nama_kategori'] ?? '') ?></div>
            <div class="prod-name"><?= htmlspecialchars($p['nama']) ?></div>
            <div class="prod-satuan"><?= htmlspecialchars($p['satuan']) ?></div>
            <div class="prod-footer">
              <div class="prod-price">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
              
              <?php if ($p['stok'] > 0): ?>
                <form method="POST" action="<?= BASEURL; ?>cart/add" onclick="event.stopPropagation()">
                  <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="qty" value="1">
                  <button type="submit" class="add-cart-btn" title="Tambah ke keranjang">+</button>
                </form>
              <?php else: ?>
                <span style="font-size:11px;color:#c0392b;font-weight:700;">Habis</span>
              <?php endif; ?>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php
$this->view('/layouts/footer-pelanggan', $data);
?>