
<div class="admin-page-header">
  <div>
    <h1>Dashboard</h1>
    <p>Selamat datang, <?= htmlspecialchars($data['admin_nama'] ?? 'Admin') ?>! Berikut ringkasan toko hari ini.</p>
  </div>
  <div class="header-actions">
    <a href="<?= BASEURL; ?>admin/pesanan" class="btn btn-primary">📋 Lihat Pesanan</a>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon gold">🌴</div>
    <div class="stat-info">
      <div class="val"><?= (int)($data['totalProduk'] ?? 0) ?></div>
      <div class="lbl">Total Produk</div>
      <?php if (isset($data['stokRendah']) && $data['stokRendah'] > 0): ?>
        <div class="trend trend-down">⚠️ <?= (int)$data['stokRendah'] ?> stok rendah</div>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon warm">🛒</div>
    <div class="stat-info">
      <div class="val"><?= (int)($data['totalPesanan'] ?? 0) ?></div>
      <div class="lbl">Total Pesanan</div>
      <?php if (isset($data['pendingPesanan']) && $data['pendingPesanan'] > 0): ?>
        <div class="trend trend-down"><?= (int)$data['pendingPesanan'] ?> menunggu konfirmasi</div>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon green">👥</div>
    <div class="stat-info">
      <div class="val"><?= (int)($data['totalPelanggan'] ?? 0) ?></div>
      <div class="lbl">Pelanggan Terdaftar</div>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon blue">💰</div>
    <div class="stat-info">
      <div class="val" style="font-size:16px;">Rp <?= number_format($data['totalOmzet'] ?? 0, 0, ',', '.'); ?></div>
      <div class="lbl">Omzet (Lunas)</div>
    </div>
  </div>
</div>

<?php 
$pendingPesanan   = (int)($data['pendingPesanan'] ?? 0);
$processedPesanan = (int)($data['processedPesanan'] ?? 0);
if ($pendingPesanan > 0 || $processedPesanan > 0): 
?>
<div class="admin-card" style="margin-bottom:16px;padding:14px 18px;">
  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <span style="font-size:13px;font-weight:700;color:var(--brown-dark);">Status Pesanan Aktif:</span>
    <?php if ($pendingPesanan > 0): ?>
      <a href="<?= BASEURL; ?>admin/pesanan?status=pending" class="badge badge-pending" style="font-size:12px;padding:5px 12px;text-decoration:none;">
        🟡 <?= $pendingPesanan ?> Menunggu Konfirmasi
      </a>
    <?php endif; ?>
    <?php if ($processedPesanan > 0): ?>
      <a href="<?= BASEURL; ?>admin/pembayaran" class="badge badge-processed" style="font-size:12px;padding:5px 12px;text-decoration:none;">
        🔵 <?= $processedPesanan ?> Menunggu Verifikasi Bayar
      </a>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;" class="dashboard-grid">

  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">🛒 Pesanan Terbaru</div>
      <a href="<?= BASEURL; ?>admin/pesanan" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <?php if (empty($data['recentOrders'])): ?>
      <div class="empty-state" style="padding:30px 0;">
        <div class="empty-icon" style="font-size:36px;">📭</div>
        <p>Belum ada pesanan masuk.</p>
      </div>
    <?php
    else: ?>
      <?php 
      $statusLabels = ['pending' => 'Menunggu', 'processed' => 'Diproses', 'paid' => 'Lunas', 'cancelled' => 'Batal'];
      foreach ($data['recentOrders'] as $o): 
      ?>
        <div class="recent-order-row">
          <div>
            <div class="ro-inv"><?= htmlspecialchars($o['invoice']) ?></div>
            <div style="font-size:11px;color:var(--text-light);"><?= htmlspecialchars($o['nama_pengguna']) ?></div>
          </div>
          <div class="ro-name" style="display:flex;align-items:center;gap:8px;">
            <span class="badge badge-<?= htmlspecialchars($o['status']) ?>">
              <?= htmlspecialchars($statusLabels[$o['status']] ?? $o['status']) ?>
            </span>
          </div>
          <div class="ro-total">Rp <?= number_format($o['total'], 0, ',', '.'); ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">📊 Penjualan per Kategori</div>
    </div>
    <div class="chart-bar-wrap">
      <?php 
      // Ambil nilai penjualan tertinggi dari data olahan controller untuk pembagi persentase bar width
      $maxSales = max(array_column($data['kategoriSales'] ?? [], 'total_terjual') ?: [1]);
      foreach (($data['kategoriSales'] ?? []) as $ks): 
        $pct = $maxSales > 0 ? round(($ks['total_terjual'] / $maxSales) * 100) : 0;
      ?>
        <div class="chart-bar-row">
          <div class="chart-bar-label"><?= htmlspecialchars(explode(' ', $ks['nama_kategori'])[0]) ?></div>
          <div class="chart-bar-track">
            <div class="chart-bar-fill" style="width:<?= max(8, $pct) ?>%">
              <?= (int)$ks['total_terjual'] ?> terjual
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($data['lowStok'])): ?>
      <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border-sand);">
        <div style="font-size:12px;font-weight:700;color:var(--danger);margin-bottom:10px;">⚠️ Stok Hampir Habis</div>
        <?php foreach ($data['lowStok'] as $l): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(201,169,110,.1);">
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
              <span><?= htmlspecialchars($l['icon']) ?></span> <?= htmlspecialchars($l['nama']) ?>
            </div>
            <span class="badge <?= $l['stok'] <= 5 ? 'badge-cancelled' : 'badge-pending' ?>">
              <?= (int)$l['stok'] ?> unit
            </span>
          </div>
        <?php endforeach; ?>
        <div style="margin-top:10px;">
          <a href="<?= BASEURL; ?>admin/stok" class="btn btn-warning btn-sm btn-block" style="text-decoration:none; text-align:center; display:block;">Kelola Stok →</a>
        </div>
      </div>
    <?php endif; ?>
  </div>

</div>

<style>
@media(max-width:700px){
  .dashboard-grid{grid-template-columns:1fr!important;}
}
</style>

