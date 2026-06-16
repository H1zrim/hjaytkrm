<?php
$this->view('/layouts/header-pelanggan', $data);
?>

<div class="hero">
  <div class="hero-bg"></div>
  <div class="hero-pattern"></div>
  <div class="hero-content">
    <div class="hero-badge">✦ Produk Unggulan Haji & Umroh</div>
    <h1>Kurma <em>Premium</em><br>dari Tanah Suci</h1>
    <p>Kurma Madinah pilihan berkualitas tinggi, langsung dari sumbernya. Cocok untuk oleh-oleh haji &amp; umroh maupun konsumsi harian keluarga.</p>
    <div class="hero-cta">
      <a href="<?= BASEURL; ?>catalog" class="btn btn-gold btn-lg">Belanja Sekarang →</a>
      <a href="<?= BASEURL; ?>catalog" class="btn btn-outline" style="color:var(--sand-light);border-color:rgba(201,169,110,.4);">Lihat Katalog</a>
    </div>
  </div>
  <div class="hero-stats">
    <div class="stat-item"><span class="stat-num">500+</span><span class="stat-lbl">Produk</span></div>
    <div class="stat-item"><span class="stat-num">10K+</span><span class="stat-lbl">Pelanggan</span></div>
    <div class="stat-item"><span class="stat-num">100%</span><span class="stat-lbl">Original</span></div>
  </div>
</div>

<div class="section">
  <div class="section-header">
    <h2 class="section-title">Kategori <span>Produk</span></h2>
    <a href="<?= BASEURL; ?>catalog" class="section-link">Lihat semua →</a>
  </div>
  <div class="cat-grid">
    <?php foreach ($data['kategoriList'] as $k): ?>
      <a href="<?= BASEURL; ?>catalog?kat=<?= $k['id'] ?>" class="cat-card">
        <span class="cat-icon"><?= htmlspecialchars($k['icon']) ?></span>
        <div class="cat-name"><?= htmlspecialchars($k['nama_kategori']) ?></div>
        <div class="cat-count"><?= $k['jml_produk'] ?> produk tersedia</div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<div class="section section-alt">
  <div class="section-header">
    <h2 class="section-title">Produk <span>Unggulan</span></h2>
    <a href="<?= BASEURL; ?>catalog" class="section-link">Lihat semua →</a>
  </div>
  <div class="products-grid">
    <?php foreach ($data['produkUnggulan'] as $p): ?>
      <a href="<?= BASEURL; ?>catalog/detail/<?= $p['id'] ?>" class="product-card" style="text-decoration:none;">
        <div class="prod-img">
          <?= htmlspecialchars($p['icon']) ?>
          <?php if ($p['badge']): ?><div class="prod-badge"><?= htmlspecialchars($p['badge']) ?></div><?php endif; ?>
        </div>
        <div class="prod-body">
          <div class="prod-cat"><?= htmlspecialchars($p['nama_kategori'] ?? '') ?></div>
          <div class="prod-name"><?= htmlspecialchars($p['nama']) ?></div>
          <div class="prod-satuan"><?= htmlspecialchars($p['satuan']) ?></div>
          <div class="prod-footer">
            <div class="prod-price">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
            
            <form method="POST" action="<?= BASEURL; ?>cart/add" onclick="event.stopPropagation()">
              <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
              <input type="hidden" name="qty" value="1">
              <button type="submit" class="add-cart-btn" title="Tambah ke keranjang">+</button>
            </form>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<div class="promo-banner">
  <div class="promo-text">
    <h2>Spesial Ramadan &amp; Haji 2026 🌙</h2>
    <p>Dapatkan diskon 15% untuk pembelian di atas Rp 150.000</p>
  </div>
  <div class="promo-code-box">
    <div class="promo-code">HAJI2026</div>
    <div class="promo-code-lbl">Kode Promo</div>
  </div>
  <a href="<?= BASEURL; ?>catalog" class="btn btn-gold">Belanja Sekarang</a>
</div>

<?php
$this->view('/layouts/footer-pelanggan', $data);
?>