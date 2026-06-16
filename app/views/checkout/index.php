<?php
$this->view('/layouts/header-pelanggan', $data);
?>

<div class="page-header">
  <div class="breadcrumb">
    <a href="<?= BASEURL; ?>">Beranda</a> › 
    <a href="<?= BASEURL; ?>cart">Keranjang</a> › 
    Checkout
  </div>
  <h1>Checkout Pesanan</h1>
</div>

<?php if (isset($data['errors']) && !empty($data['errors'])): ?>
  <div style="padding:0 32px;">
    <div class="alert alert-danger">
      <?php foreach ($data['errors'] as $e): ?>
        ❌ <?= htmlspecialchars($e) ?><br>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<div class="co-layout">
  <form method="POST" action="<?= BASEURL; ?>checkout/proses" id="mainCheckoutForm">
    
    <div class="co-step">
      <div class="co-step-hdr">
        <div class="co-step-num">1</div>
        <h3>Informasi Pengiriman</h3>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Nama Penerima *</label>
          <input type="text" name="nama_penerima" class="form-control" required
                 value="<?= htmlspecialchars($data['input']['nama_penerima'] ?? $data['pelanggan']['nama_penerima'] ?? $data['pelanggan']['nama_pengguna'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>No. Handphone *</label>
          <input type="tel" name="no_hp" class="form-control" required
                 value="<?= htmlspecialchars($data['input']['no_hp'] ?? $data['pelanggan']['no_hp'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Alamat Lengkap *</label>
        <input type="text" name="alamat" class="form-control" required
               value="<?= htmlspecialchars($data['input']['alamat'] ?? $data['pelanggan']['alamat'] ?? '') ?>"
               placeholder="Jl. nama jalan, no. rumah, RT/RW, kelurahan, kota">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Kode Pos</label>
          <input type="text" name="kode_pos" class="form-control"
                 value="<?= htmlspecialchars($data['input']['kode_pos'] ?? $data['pelanggan']['kode_pos'] ?? '') ?>" placeholder="75xxx">
        </div>
        <div class="form-group">
          <label>Catatan Kurir (opsional)</label>
          <input type="text" name="catatan" class="form-control"
                 value="<?= htmlspecialchars($data['input']['catatan'] ?? '') ?>" placeholder="Cth: Hubungi sebelum kirim">
        </div>
      </div>
    </div>

    <div class="co-step">
      <div class="co-step-hdr">
        <div class="co-step-num">2</div>
        <h3>Metode Pembayaran</h3>
      </div>
      
      <?php 
        // Mengamankan penandaan status tombol aktif metode pembayaran
        $metodeTerpilih = $data['input']['metode_bayar'] ?? 'qris'; 
      ?>
      <div class="payment-opts">
        <label class="pay-opt <?= $metodeTerpilih === 'qris' ? 'active' : '' ?>" onclick="selectPay(this,'qris')">
          <input type="radio" name="metode_bayar" value="qris" style="display:none;" <?= $metodeTerpilih === 'qris' ? 'checked' : '' ?>>
          <div class="pay-icon">📱</div>
          <div class="pay-label">QRIS</div>
        </label>
        <label class="pay-opt <?= $metodeTerpilih === 'transfer' ? 'active' : '' ?>" onclick="selectPay(this,'transfer')">
          <input type="radio" name="metode_bayar" value="transfer" style="display:none;" <?= $metodeTerpilih === 'transfer' ? 'checked' : '' ?>>
          <div class="pay-icon">🏦</div>
          <div class="pay-label">Transfer Bank</div>
        </label>
        <label class="pay-opt <?= $metodeTerpilih === 'cod' ? 'active' : '' ?>" onclick="selectPay(this,'cod')">
          <input type="radio" name="metode_bayar" value="cod" style="display:none;" <?= $metodeTerpilih === 'cod' ? 'checked' : '' ?>>
          <div class="pay-icon">💵</div>
          <div class="pay-label">COD</div>
        </label>
      </div>
      
      <div class="pay-info-box" id="payInfoBox" style="margin-top:12px;">
        📱 Scan QRIS saat pesanan dikonfirmasi oleh admin.
      </div>
    </div>

    <div class="co-step">
      <div class="co-step-hdr">
        <div class="co-step-num">3</div>
        <h3>Konfirmasi Item Pesanan</h3>
      </div>
      <?php foreach ($data['cart'] as $item): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid rgba(201,169,110,.1);">
          <span style="font-size:22px;"><?= htmlspecialchars($item['icon']) ?></span>
          <div style="flex:1;">
            <div style="font-size:13px;font-weight:600;color:var(--brown-dark);"><?= htmlspecialchars($item['nama_produk']) ?></div>
            <div style="font-size:11px;color:var(--text-light);"><?= htmlspecialchars($item['satuan']) ?> × <?= $item['qty'] ?></div>
          </div>
          <strong style="font-size:13px;">Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.'); ?></strong>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="display:none;" class="mobile-submit-btn">
      <button type="submit" class="btn btn-primary btn-lg btn-block">✅ Buat Pesanan</button>
    </div>
  </form>

  <div class="order-summary" style="position:sticky;top:72px;height:fit-content;">
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
        <span class="os-label">Diskon</span>
        <span class="os-value" style="color:#27ae60;">− Rp <?= number_format($data['diskon'], 0, ',', '.'); ?></span>
      </div>
    <?php endif; ?>
    <div class="os-row os-total">
      <span class="os-label">Total</span>
      <span class="os-value">Rp <?= number_format($data['total'], 0, ',', '.'); ?></span>
    </div>
    
    <button type="submit" form="mainCheckoutForm" class="btn btn-primary btn-lg btn-block" style="margin-top:16px;">
      ✅ Buat Pesanan
    </button>
    <a href="<?= BASEURL; ?>cart" style="display:block;text-align:center;margin-top:10px;font-size:12px;color:var(--text-light); text-decoration:none;">← Kembali ke Keranjang</a>
  </div>
</div>

<script>
const payInfo = {
  qris: '📱 Scan QRIS saat pesanan dikonfirmasi oleh admin.',
  transfer: '🏦 Transfer ke BCA 1234567890 a.n. Haji Ayat Kurma. Upload bukti di halaman Pesanan Saya.',
  cod: '💵 Bayar tunai saat paket tiba. Siapkan uang pas ya!'
};
function selectPay(el, val) {
  document.querySelectorAll('.pay-opt').forEach(o => o.classList.remove('active'));
  el.classList.add('active');
  el.querySelector('input').checked = true;
  document.getElementById('payInfoBox').textContent = payInfo[val];
}
</script>

<style>
@media(max-width:768px){
  .co-layout{grid-template-columns:1fr!important;padding:16px;}
  .mobile-submit-btn{display:block!important;margin-top:4px;}
  .order-summary .btn-block{display:none;}
}
</style>

<?php
$this->view('/layouts/footer-pelanggan', $data);
?>