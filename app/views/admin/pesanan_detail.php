<?php
if (!isset($data['pesanan']) || empty($data['pesanan'])): 
?>
  <div class="alert alert-danger">Pesanan tidak ditemukan atau data korup.</div>
<?php 
  exit; 
  endif; 
  
  // Memetakan variabel agar kode di bawah tetap ringkas
  $p = $data['pesanan'];
  $items = $data['items'] ?? [];
  $statusLabels = ['pending' => 'Menunggu', 'processed' => 'Diproses', 'paid' => 'Lunas', 'cancelled' => 'Dibatalkan'];
  $metLabels    = ['qris' => '📱 QRIS', 'transfer' => '🏦 Transfer Bank', 'cod' => '💵 COD'];
?>

<div class="order-meta-grid">
  <div class="order-meta-item">
    <div class="lbl">Invoice</div>
    <div class="val"><?= htmlspecialchars($p['invoice']) ?></div>
  </div>
  <div class="order-meta-item">
    <div class="lbl">Status</div>
    <div class="val">
      <span class="badge badge-<?= htmlspecialchars($p['status']) ?>">
        <?= htmlspecialchars($statusLabels[$p['status']] ?? $p['status']) ?>
      </span>
    </div>
  </div>
  <div class="order-meta-item">
    <div class="lbl">Pelanggan</div>
    <div class="val"><?= htmlspecialchars($p['nama_pengguna']) ?></div>
  </div>
  <div class="order-meta-item">
    <div class="lbl">Tanggal</div>
    <div class="val"><?= date('d M Y H:i', strtotime($p['created_at'])) ?></div>
  </div>
  <div class="order-meta-item">
    <div class="lbl">Metode Bayar</div>
    <div class="val"><?= htmlspecialchars($metLabels[$p['metode_bayar']] ?? $p['metode_bayar']) ?></div>
  </div>
  <div class="order-meta-item">
    <div class="lbl">No. HP Penerima</div>
    <div class="val"><?= htmlspecialchars($p['no_hp'] ?: ($p['pelanggan_hp'] ?? '-')) ?></div>
  </div>
</div>

<div class="order-meta-item" style="margin-bottom:12px;">
  <div class="lbl">Alamat Pengiriman</div>
  <div class="val" style="font-size:13px;font-weight:400;margin-top:3px;">
    <?= htmlspecialchars($p['alamat_kirim']) ?> <?= !empty($p['kode_pos']) ? '(' . htmlspecialchars($p['kode_pos']) . ')' : '' ?>
  </div>
</div>

<?php if (!empty($p['catatan'])): ?>
  <div class="order-meta-item" style="margin-bottom:12px;">
    <div class="lbl">Catatan</div>
    <div class="val" style="font-weight:400;font-size:13px;"><?= htmlspecialchars($p['catatan']) ?></div>
  </div>
<?php endif; ?>

<div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);margin-bottom:8px;">Detail Produk</div>
<table class="order-items-tbl" style="margin-bottom:12px;">
  <thead>
    <tr>
      <th>Produk</th>
      <th>Satuan</th>
      <th>Harga</th>
      <th>Qty</th>
      <th>Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= htmlspecialchars($item['icon'] ?? '📦') ?> <?= htmlspecialchars($item['nama_produk']) ?></td>
        <td><?= htmlspecialchars($item['satuan']) ?></td>
        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
        <td>×<?= (int)$item['qty'] ?></td>
        <td><strong>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></strong></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div style="background:var(--sand-lightest);border-radius:8px;padding:12px 14px;">
  <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
    <span style="color:var(--text-light);">Subtotal</span>
    <strong>Rp <?= number_format($p['subtotal'], 0, ',', '.') ?></strong>
  </div>
  
  <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
    <span style="color:var(--text-light);">Ongkos Kirim</span>
    <strong><?= $p['ongkir'] == 0 ? 'GRATIS' : 'Rp ' . number_format($p['ongkir'], 0, ',', '.') ?></strong>
  </div>
  
  <?php if (isset($p['diskon']) && $p['diskon'] > 0): ?>
    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
      <span style="color:var(--text-light);">Diskon</span>
      <strong style="color:#27ae60;">- Rp <?= number_format($p['diskon'], 0, ',', '.') ?></strong>
    </div>
  <?php endif; ?>
  
  <div style="display:flex;justify-content:space-between;font-size:15px;border-top:1px solid var(--border-sand);padding-top:8px;margin-top:5px;">
    <strong>Total</strong>
    <strong style="font-family:'Playfair Display',serif;color:var(--accent-warm);">
      Rp <?= number_format($p['total'], 0, ',', '.') ?>
    </strong>
  </div>
</div>