<?php
$this->view('/layouts/header-admin', $data);
$this->view('/layouts/sidebar-admin', $data);
?>

<div class="admin-page-header">
  <div>
    <h1>Kelola Stok</h1>
    <p>Tambah atau sesuaikan stok produk secara langsung.</p>
  </div>
  <div class="header-actions">
    <?php if (isset($data['lowCount']) && $data['lowCount'] > 0): ?>
      <a href="<?= BASEURL; ?>admin/stok?low=1" class="btn btn-warning">⚠️ <?= $data['lowCount'] ?> Stok Rendah</a>
    <?php endif; ?>
  </div>
</div>

<form method="GET" action="<?= BASEURL; ?>admin/stok" class="filter-bar">
  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" name="q" class="form-control" placeholder="Cari produk..." value="<?= htmlspecialchars($data['search'] ?? '') ?>">
  </div>
  
  <select name="kat" class="filter-select" onchange="this.form.submit()">
    <option value="">Semua Kategori</option>
    <?php foreach (($data['kategoriAll'] ?? []) as $k): ?>
      <option value="<?= $k['id'] ?>" <?= (isset($data['katFilter']) && $data['katFilter'] == $k['id']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($k['nama_kategori']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  
  <?php if (isset($data['showLow']) && $data['showLow']): ?>
    <a href="<?= BASEURL; ?>admin/stok" class="btn btn-outline">✕ Hapus Filter</a>
  <?php else: ?>
    <button type="submit" class="btn btn-outline">Filter</button>
  <?php endif; ?>
</form>

<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">📦 Daftar Stok Produk</div>
    <span style="font-size:13px;color:var(--text-light);"><?= count($data['produkList'] ?? []) ?> produk</span>
  </div>

  <?php if (empty($data['produkList'])): ?>
    <div class="empty-state">
      <div class="empty-icon">📦</div>
      <h3>Tidak ada produk ditemukan</h3>
    </div>
  <?php else: ?>
    <?php foreach ($data['produkList'] as $p): ?>
      <div class="stok-row">
        
        <div class="stok-info">
          <div class="stok-icon"><?= htmlspecialchars($p['icon']) ?></div>
          <div>
            <div class="stok-name"><?= htmlspecialchars($p['nama']) ?></div>
            <div class="stok-cat"><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?> · <?= htmlspecialchars($p['satuan']) ?></div>
          </div>
        </div>

        <div style="text-align:center;">
          <div class="stok-current"><?= (int)$p['stok'] ?></div>
          <div style="font-size:10px;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;">unit</div>
          <?php if ($p['stok'] <= 0): ?>
            <span class="badge badge-cancelled" style="font-size:10px;margin-top:4px;">Habis</span>
          <?php elseif ($p['stok'] <= 5): ?>
            <span class="badge badge-cancelled" style="font-size:10px;margin-top:4px;">Kritis</span>
          <?php elseif ($p['stok'] <= 10): ?>
            <span class="badge badge-pending" style="font-size:10px;margin-top:4px;">Rendah</span>
          <?php else: ?>
            <span class="badge badge-ok" style="font-size:10px;margin-top:4px;">Tersedia</span>
          <?php endif; ?>
        </div>

        <div class="stok-controls">
          
          <form method="POST" action="<?= BASEURL; ?>admin/stok/prosesPenyesuaian" style="display:flex;align-items:center;gap:6px;">
            <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
            <input type="hidden" name="mode" value="add">
            <button type="submit" name="adj" value="-10" class="btn btn-danger btn-xs">−10</button>
            <button type="submit" name="adj" value="-1" class="btn btn-outline btn-xs">−1</button>
            <button type="submit" name="adj" value="1" class="btn btn-outline btn-xs">+1</button>
            <button type="submit" name="adj" value="10" class="btn btn-success btn-xs">+10</button>
          </form>
          
          <form method="POST" action="<?= BASEURL; ?>admin/stok/prosesPenyesuaian" style="display:flex;align-items:center;gap:6px;">
            <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
            <input type="hidden" name="mode" value="set">
            <input type="number" name="adj" class="form-control" min="0"
                   style="width:70px;padding:6px 8px;text-align:center;"
                   placeholder="Set" title="Set stok langsung" required>
            <button type="submit" class="btn btn-primary btn-xs">Set</button>
          </form>

        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php
$this->view('/layouts/footer-admin', $data);
?>