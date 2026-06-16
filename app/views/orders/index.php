<?php
$this->view('/layouts/header-pelanggan', $data);
?>

<div class="page-header">
  <div class="breadcrumb"><a href="<?= BASEURL; ?>">Beranda</a> › Pesanan Saya</div>
  <h1>Pesanan Saya</h1>
  <p>Pantau status dan riwayat belanja Anda</p>
</div>

<?php if (isset($data['errors']) && !empty($data['errors'])): ?>
  <div style="padding:0 32px;">
    <div class="alert alert-danger">
      <?php foreach ($data['errors'] as $e): ?>❌ <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<div class="orders-wrap">
  <div class="order-tabs">
    <?php
    $tabs = ['all' => 'Semua', 'pending' => 'Menunggu', 'processed' => 'Diproses', 'paid' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $statusFilter = $data['statusFilter'] ?? 'all';
    
    foreach ($tabs as $s => $l):
      // Menghitung jumlah badge counter dinamis yang dikirim dari controller
      $cnt = ($s === 'all') ? array_sum($data['statusCounts'] ?? []) : ($data['statusCounts'][$s] ?? 0);
    ?>
      <a href="<?= BASEURL; ?>orders?status=<?= $s ?>"
         class="order-tab <?= $statusFilter === $s ? 'active' : '' ?>">
        <?= htmlspecialchars($l) ?>
        <?php if ($cnt > 0): ?>
          <span style="background:rgba(201,169,110,.15);border-radius:10px;padding:1px 6px;font-size:10px;margin-left:3px;"><?= $cnt ?></span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($data['pesananList'])): ?>
    <div class="empty-state">
      <div class="ei">📦</div>
      <h3>Belum ada riwayat pesanan</h3>
      <p>Yuk, mulai belanja dan pesanan Anda akan muncul di sini.</p>
      <a href="<?= BASEURL; ?>catalog" class="btn btn-primary">Mulai Belanja →</a>
    </div>
  <?php else: ?>
    <?php 
    $statusLabels = ['pending' => 'Menunggu', 'processed' => 'Diproses', 'paid' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $metodeLabels = ['qris' => '📱 QRIS', 'transfer' => '🏦 Transfer Bank', 'cod' => '💵 COD'];
    
    foreach ($data['pesananList'] as $p): 
    ?>
      <div class="order-card">
        <div class="oc-header">
          <div>
            <div class="oc-inv"><?= htmlspecialchars($p['invoice']) ?></div>
            <div class="oc-date">📅 <?= date('d M Y H:i', strtotime($p['created_at'])) ?></div>
            <div style="font-size:11px;color:var(--text-light);margin-top:2px;">
              <?= htmlspecialchars($metodeLabels[$p['metode_bayar']] ?? $p['metode_bayar']) ?>
            </div>
          </div>
          <span class="status-badge s-<?= htmlspecialchars($p['status']) ?>">
            <?= htmlspecialchars($statusLabels[$p['status']] ?? $p['status']) ?>
          </span>
        </div>

        <?php foreach ($p['items'] as $item): ?>
          <div class="oc-item">
            <div class="oc-item-icon"><?= htmlspecialchars($item['icon'] ?? '📦') ?></div>
            <div>
              <div class="oc-item-name"><?= htmlspecialchars($item['nama_produk']) ?></div>
              <div class="oc-item-qty"><?= htmlspecialchars($item['satuan']) ?> × <?= $item['qty'] ?></div>
            </div>
            <div class="oc-item-price">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></div>
          </div>
        <?php endforeach; ?>

        <div class="oc-footer">
          <div>
            <div class="oc-total-lbl">Total Pesanan</div>
            <div class="oc-total-val">Rp <?= number_format($p['total'], 0, ',', '.'); ?></div>
          </div>
          
          <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <?php if ($p['status'] === 'pending' && $p['metode_bayar'] !== 'cod'): ?>
              <button class="upload-btn" onclick="openUploadModal(<?= $p['id'] ?>,'<?= htmlspecialchars($p['invoice']) ?>')">
                <?= $p['bukti_bayar'] ? '📎 Ganti Bukti' : '📤 Upload Bukti Bayar' ?>
              </button>
              <?php if ($p['bukti_bayar']): ?>
                <span style="font-size:11px;color:#27ae60;font-weight:700;">✅ Bukti terkirim</span>
              <?php endif; ?>
            <?php elseif ($p['status'] === 'pending'): ?>
              <span style="font-size:11px;color:var(--accent-warm);">⏳ Menunggu konfirmasi admin</span>
            <?php elseif ($p['status'] === 'paid'): ?>
              <span style="font-size:11px;color:#27ae60;font-weight:700;">✅ Pembayaran terverifikasi</span>
            <?php elseif ($p['status'] === 'cancelled'): ?>
              <span style="font-size:11px;color:#c0392b;font-weight:700;">❌ Pesanan dibatalkan</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="modal-overlay" id="uploadModal">
  <div class="modal-box">
    <h3>📤 Upload Bukti Pembayaran</h3>
    <p id="uploadModalInv" style="font-size:12px;color:var(--text-light);margin-bottom:16px;"></p>
    
    <form method="POST" action="<?= BASEURL; ?>orders/upload" enctype="multipart/form-data">
      <input type="hidden" name="pesanan_id" id="uploadPesananId">
      
      <div class="form-group">
        <label>File Bukti (JPG, PNG, PDF — maks 3MB)</label>
        <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.webp" required>
      </div>
      
      <div style="display:flex;gap:10px;margin-top:4px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('uploadModal')">Batal</button>
        <button type="submit" class="btn btn-primary" style="flex:1;">📤 Unggah Sekarang</button>
      </div>
    </form>
  </div>
</div>

<script>
function openUploadModal(id, inv) {
  document.getElementById('uploadPesananId').value = id;
  document.getElementById('uploadModalInv').textContent = 'Pesanan: ' + inv;
  openModal('uploadModal'); // Memanggil fungsi global modal management aset template Anda
}
</script>

<?php
$this->view('/layouts/footer-pelanggan', $data);
?>