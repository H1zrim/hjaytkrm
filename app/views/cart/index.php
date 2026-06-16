<?php
$this->view('/layouts/header-pelanggan', $data);
?>

<div class="page-header">
  <div class="breadcrumb"><a href="<?= BASEURL; ?>">Beranda</a> › Keranjang Belanja</div>
  <h1>Keranjang Belanja</h1>
</div>

<div class="cart-layout">
  <div>
    <?php if (empty($data['cart'])): ?>
      <div class="empty-state">
        <div class="ei">🛒</div>
        <h3>Keranjang Anda kosong</h3>
        <p>Yuk, mulai belanja kurma dan produk haji pilihan terbaik!</p>
        <a href="<?= BASEURL; ?>catalog" class="btn btn-primary">Lihat Katalog →</a>
      </div>
    <?php else: ?>
      <div class="cart-header" style="display:grid;grid-template-columns:2fr 1fr 120px 1fr 32px;gap:12px;">
        <span>Produk</span><span>Harga Satuan</span><span>Jumlah</span><span>Subtotal</span><span></span>
      </div>

      <?php foreach ($data['cart'] as $key => $item): ?>
        <div class="cart-item">
          <div class="ci-name">
            <div class="ci-icon"><?= htmlspecialchars($item['icon']) ?></div>
            <div class="ci-info">
              <h4><?= htmlspecialchars($item['nama_produk']) ?></h4>
              <p><?= htmlspecialchars($item['satuan']) ?></p>
              <p style="font-family:'Playfair Display',serif;font-weight:700;color:var(--brown-dark);font-size:14px;margin-top:4px;" class="mobile-price">
                Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
              </p>
            </div>
          </div>
          
          <div class="ci-price" style="font-size:14px;">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></div>
          
          <div>
            <form method="POST" action="<?= BASEURL; ?>cart/update" style="display:inline;">
              <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
              <div class="ci-qty-ctrl">
                <button type="submit" name="qty" value="<?= max(1, $item['qty'] - 1) ?>" class="cq-btn">−</button>
                <span class="cq-num"><?= $item['qty'] ?></span>
                <button type="submit" name="qty" value="<?= min($item['stok'], $item['qty'] + 1) ?>" class="cq-btn">+</button>
              </div>
            </form>
          </div>
          
          <div class="ci-total">Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.'); ?></div>
          
          <form method="POST" action="<?= BASEURL; ?>cart/remove">
            <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
            <button type="submit" class="del-btn" title="Hapus">🗑</button>
          </form>
        </div>
      <?php endforeach; ?>

      <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;flex-wrap:wrap;gap:10px;">
        <a href="<?= BASEURL; ?>catalog" class="btn btn-outline btn-sm">← Lanjut Belanja</a>
        <form method="POST" action="<?= BASEURL; ?>cart/clear">
          <button type="submit" class="btn btn-outline btn-sm" style="color:#c0392b;"
                  onclick="return confirm('Kosongkan seluruh keranjang?')">🗑 Kosongkan</button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <div class="order-summary">
    <div class="os-title">Ringkasan Pesanan</div>
    <div class="os-row">
        <span class="os-label">Subtotal</span>
        <span class="os-value">Rp <?= number_format($data['subtotal'], 0, ',', '.'); ?></span>
    </div>
    
    <div class="os-row">
      <span class="os-label">Ongkos Kirim</span>
      <span class="os-value" style="<?= $data['ongkir'] == 0 ? 'color:#27ae60;' : '' ?>">
        <?= $data['ongkir'] == 0 ? 'GRATIS 🎉' : 'Rp ' . number_format($data['ongkir'], 0, ',', '.'); ?>
      </span>
    </div>
    
    <?php if ($data['diskon'] > 0): ?>
      <div class="os-row">
        <span class="os-label">Diskon (<?= htmlspecialchars($data['promo']['kode']) ?>)</span>
        <span class="os-value" style="color:#27ae60;">− Rp <?= number_format($data['diskon'], 0, ',', '.'); ?></span>
      </div>
    <?php endif; ?>
    
    <?php if ($data['subtotal'] < 150000 && !empty($data['cart'])): ?>
      <div style="font-size:11px;color:var(--accent-warm);margin-bottom:10px;padding:8px;background:rgba(212,120,42,.08);border-radius:6px;">
        🚚 Tambah Rp <?= number_format(150000 - $data['subtotal'], 0, ',', '.'); ?> lagi untuk gratis ongkir!
      </div>
    <?php endif; ?>
    
    <div class="os-row os-total">
      <span class="os-label">Total</span>
      <span class="os-value">Rp <?= number_format($data['total'], 0, ',', '.'); ?></span>
    </div>

    <form method="POST" action="<?= BASEURL; ?>cart/promo" class="promo-input-row">
      <input type="text" name="kode_promo" placeholder="Kode promo" value="<?= htmlspecialchars($data['promo']['kode'] ?? '') ?>">
      <button type="submit"><?= $data['promo'] ? '✓ Aktif' : 'Pakai' ?></button>
    </form>
    
    <?php if ($data['promo']): ?>
      <div style="font-size:11px;color:#27ae60;margin-bottom:10px;">✅ Promo <?= htmlspecialchars($data['promo']['kode']) ?> aktif (–<?= $data['promo']['persen'] ?>%)</div>
    <?php endif; ?>

    <?php if (!empty($data['cart'])): ?>
      <a href="<?= BASEURL; ?>checkout" class="btn btn-primary btn-lg btn-block">Lanjut ke Pembayaran →</a>
    <?php else: ?>
      <button class="btn btn-primary btn-lg btn-block" disabled style="opacity:.5;">Keranjang Kosong</button>
    <?php endif; ?>
  </div>
</div>

<style>
@media(min-width:769px){ .mobile-price{display:none!important;} }
@media(max-width:768px){
  .cart-layout{grid-template-columns:1fr!important;}
  .cart-header{display:none!important;}
  .cart-item{display:flex;flex-direction:column;gap:10px;}
  .ci-price{display:none;}
}
</style>

<?php
$this->view('/layouts/footer-pelanggan', $data);
?>