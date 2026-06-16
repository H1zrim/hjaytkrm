<?php
$this->view('/layouts/header-pelanggan', $data);
?>

<div class="page-header">
  <div class="breadcrumb">
    <a href="<?= BASEURL; ?>">Beranda</a> › 
    <a href="<?= BASEURL; ?>catalog">Katalog</a> › 
    <?= htmlspecialchars(mb_substr($data['produk']['nama'], 0, 30)) ?>
  </div>
  <h1><?= htmlspecialchars($data['produk']['nama']) ?></h1>
</div>

<div class="detail-layout">
  <div>
    <div class="detail-img-main" id="mainImg"><?= htmlspecialchars($data['produk']['icon']) ?></div>
    <div class="detail-thumbs">
      <div class="thumb active" onclick="document.getElementById('mainImg').textContent='<?= htmlspecialchars($data['produk']['icon']) ?>'"><?= htmlspecialchars($data['produk']['icon']) ?></div>
      <div class="thumb" onclick="document.getElementById('mainImg').textContent='📦'">📦</div>
      <div class="thumb" onclick="document.getElementById('mainImg').textContent='🏷️'">🏷️</div>
    </div>
  </div>

  <div>
    <?php if ($data['produk']['badge']): ?>
      <div class="detail-badge"><?= htmlspecialchars($data['produk']['badge']) ?></div>
    <?php endif; ?>
    
    <h1 class="detail-name"><?= htmlspecialchars($data['produk']['nama']) ?></h1>
    
    <div class="detail-meta">
      <span class="stars">★★★★★</span>
      <span style="color:var(--text-light);">4.9 (128 ulasan)</span>
      <span style="color:var(--border-sand);">|</span>
      <?php if ($data['produk']['stok'] > 0): ?>
        <span style="color:#27ae60;font-weight:700;font-size:13px;">✓ Stok tersedia (<?= $data['produk']['stok'] ?> unit)</span>
      <?php else: ?>
        <span style="color:#c0392b;font-weight:700;font-size:13px;">✗ Stok habis</span>
      <?php endif; ?>
    </div>
    
    <p class="detail-desc"><?= htmlspecialchars($data['produk']['deskripsi']) ?></p>

    <form method="POST" action="<?= BASEURL; ?>cart/add" id="formDetail">
      <input type="hidden" name="produk_id" value="<?= $data['produk']['id'] ?>">

      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);margin-bottom:8px;">Pilih Satuan</div>
      <div class="weight-opts" id="weightOpts">
        <?php
        $satuanList = [$data['produk']['satuan']];
        // Logika pembentukan variasi opsi tampilan pilihan satuan berdasarkan text database
        if (strpos($data['produk']['satuan'], 'g') !== false && !strpos($data['produk']['satuan'], 'kg')) {
            $base = (int)$data['produk']['satuan'];
            $satuanList = [$base.'g', ($base*2).'g', '1kg'];
        } elseif (strpos($data['produk']['satuan'], 'ml') !== false) {
            $satuanList = ['250ml','500ml','1L'];
        } elseif ($data['produk']['satuan'] === 'pcs' || $data['produk']['satuan'] === 'buah') {
            $satuanList = ['1 pcs','2 pcs','5 pcs'];
        }
        foreach ($satuanList as $i => $s):
        ?>
          <button type="button" class="weight-opt <?= $i === 0 ? 'active' : '' ?>"
                  onclick="setWeight(this,'<?= htmlspecialchars($s) ?>')"><?= htmlspecialchars($s) ?></button>
        <?php endforeach; ?>
      </div>
      <input type="hidden" name="satuan" id="selectedSatuan" value="<?= htmlspecialchars($satuanList[0]) ?>">

      <div class="detail-price" id="detailPrice">Rp <?= number_format($data['produk']['harga'], 0, ',', '.'); ?></div>
      <div class="detail-per">per <?= htmlspecialchars($satuanList[0]) ?></div>

      <div class="qty-row">
        <span class="qty-label">Jumlah:</span>
        <div class="qty-ctrl">
          <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
          <span class="qty-num" id="qtyNum">1</span>
          <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
        </div>
        <input type="hidden" name="qty" id="qtyInput" value="1">
        <span class="stok-info">Maks: <?= $data['produk']['stok'] ?> unit</span>
      </div>

      <?php if ($data['produk']['stok'] > 0): ?>
        <div class="detail-actions">
          <button type="submit" class="btn-add-cart">🛒 Tambah ke Keranjang</button>
          
          <button type="submit" name="beli_sekarang" value="1" class="btn-beli">Beli Sekarang →</button>
        </div>
      <?php else: ?>
        <div style="background:#f8d7da;border:1px solid #f5c6cb;border-radius:8px;padding:12px;text-align:center;font-size:13px;color:#721c24;font-weight:700;">
          ⚠️ Stok produk ini sedang habis
        </div>
      <?php endif; ?>
    </form>

    <div class="detail-keunggulan">
      <span>✅ 100% Original</span>
      <span>🌿 Alami &amp; Halal</span>
      <span>📦 Dikemas Rapi</span>
      <span>🚚 Gratis Ongkir &gt;Rp150rb</span>
    </div>
  </div>
</div>

<script>
// Penampung nilai harga dan stok awal dari variabel data master
const hargaSatuan = <?= (int)$data['produk']['harga'] ?>;
const maxStok = <?= (int)$data['produk']['stok'] ?>;
let qty = 1;

function changeQty(d){
  qty = Math.max(1, Math.min(maxStok, qty + d));
  document.getElementById('qtyNum').textContent = qty;
  document.getElementById('qtyInput').value = qty;
  
  // Kalkulasi total harga akumulasi kuantitas secara live di sisi klien
  document.getElementById('detailPrice').textContent = 'Rp ' + (hargaSatuan * qty).toLocaleString('id-ID');
}

function setWeight(btn, val){
  document.querySelectorAll('.weight-opt').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('selectedSatuan').value = val;
}
</script>

<?php
$this->view('/layouts/footer-pelanggan', $data);
?>