
<div class="admin-page-header">
  <div>
    <h1>Kelola Pesanan</h1>
    <p>Konfirmasi, pantau, dan kelola semua pesanan pelanggan.</p>
  </div>
</div>

<div style="display:flex;gap:0;border-bottom:1px solid var(--border-sand);margin-bottom:16px;overflow-x:auto;">
  <?php
  $tabs = [
    [''          => 'Semua'],
    ['pending'   => 'Menunggu'],
    ['processed' => 'Diproses'],
    ['paid'      => 'Lunas'],
    ['cancelled' => 'Dibatalkan']
  ];
  
  $currentStatus = $data['statusFilter'] ?? '';
  $statusCounts  = $data['statusCounts'] ?? [];

  foreach ($tabs as $tab):
    $s = key($tab);
    $l = current($tab);
    $active = $currentStatus === $s ? 'border-bottom:2px solid var(--accent-warm);color:var(--accent-warm);' : '';
    $cnt = $s ? ($statusCounts[$s] ?? 0) : array_sum($statusCounts);
  ?>
    <a href="<?= BASEURL; ?>admin/pesanan?status=<?= $s ?>" style="padding:9px 16px;font-size:13px;font-weight:600;color:var(--text-mid);white-space:nowrap;<?= $active ?>">
      <?= $l ?> 
      <?php if ($cnt > 0): ?>
        <span style="background:rgba(201,169,110,.2);border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;"><?= $cnt ?></span>
      <?php endif; ?>
    </a>
  <?php endforeach; ?>
</div>

<form method="GET" action="<?= BASEURL; ?>admin/pesanan" class="filter-bar">
  <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" name="q" class="form-control" placeholder="Cari invoice atau nama pelanggan..." value="<?= htmlspecialchars($data['search'] ?? '') ?>">
  </div>
  <button type="submit" class="btn btn-outline">Cari</button>
  
  <?php if (!empty($data['search'])): ?>
    <a href="<?= BASEURL; ?>admin/pesanan?status=<?= htmlspecialchars($currentStatus) ?>" class="btn btn-ghost">✕</a>
  <?php endif; ?>
</form>

<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">🛒 Daftar Pesanan</div>
    <span style="font-size:13px;color:var(--text-light);"><?= count($data['pesananList'] ?? []) ?> pesanan</span>
  </div>
  
  <?php if (empty($data['pesananList'])): ?>
    <div class="empty-state">
      <div class="empty-icon">📭</div>
      <h3>Tidak ada pesanan</h3>
      <p>Pesanan yang masuk akan tampil di sini.</p>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="tbl">
        <thead>
          <tr>
            <th>Invoice</th>
            <th>Pelanggan</th>
            <th>Metode Bayar</th>
            <th>Total</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $statusLabels = ['pending' => 'Menunggu', 'processed' => 'Diproses', 'paid' => 'Lunas', 'cancelled' => 'Dibatalkan'];
          foreach ($data['pesananList'] as $p): 
          ?>
            <tr>
              <td><strong><?= htmlspecialchars($p['invoice']) ?></strong></td>
              <td>
                <div><?= htmlspecialchars($p['nama_pengguna']) ?></div>
                <div style="font-size:11px;color:var(--text-light);"><?= htmlspecialchars($p['email']) ?></div>
              </td>
              <td>
                <?php 
                $met = ['qris' => '📱 QRIS', 'transfer' => '🏦 Transfer', 'cod' => '💵 COD'];
                echo htmlspecialchars($met[$p['metode_bayar']] ?? $p['metode_bayar']); 
                ?>
              </td>
              <td><strong>Rp <?= number_format($p['total'], 0, ',', '.'); ?></strong></td>
              <td>
                <span class="badge badge-<?= htmlspecialchars($p['status']) ?>">
                  <?= htmlspecialchars($statusLabels[$p['status']] ?? $p['status']) ?>
                </span>
              </td>
              <td style="font-size:12px;color:var(--text-light);">
                <?= date('d M Y H:i', strtotime($p['created_at'])) ?>
              </td>
              <td>
                <div class="tbl-actions">
                  <button class="btn btn-info btn-xs" onclick="openDetailModal(<?= $p['id'] ?>)">👁 Detail</button>
                  
                  <?php if ($p['status'] === 'pending'): ?>
                    <a href="<?= BASEURL; ?>admin/pesanan/prosesAksi/konfirmasi/<?= $p['id'] ?>"
                       class="btn btn-success btn-xs"
                       onclick="return confirm('Konfirmasi pesanan <?= htmlspecialchars($p['invoice']) ?>?')">✅ Konfirmasi</a>
                    
                    <button class="btn btn-danger btn-xs"
                            onclick="confirmDelete('<?= BASEURL; ?>admin/pesanan/prosesAksi/batalkan/<?= $p['id'] ?>','Batalkan pesanan <?= htmlspecialchars($p['invoice']) ?> dan kembalikan stok?')">❌ Batalkan</button>
                  
                  <?php elseif ($p['status'] === 'processed'): ?>
                    <a href="<?= BASEURL; ?>admin/pembayaran/verifikasi/<?= $p['id'] ?>" class="btn btn-warning btn-xs">💳 Verifikasi</a>
                    
                    <button class="btn btn-danger btn-xs"
                            onclick="confirmDelete('<?= BASEURL; ?>admin/pesanan/prosesAksi/batalkan/<?= $p['id'] ?>','Batalkan pesanan <?= htmlspecialchars($p['invoice']) ?>?')">❌ Batalkan</button>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="modal-overlay" id="detailModal">
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <h3>📋 Detail Pesanan</h3>
      <button class="modal-close" onclick="document.getElementById('detailModal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body" id="detailModalBody">
      <div style="text-align:center;padding:30px;color:var(--text-light);">Memuat detail...</div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('detailModal').classList.remove('open')">Tutup</button>
    </div>
  </div>
</div>

<script>
function openDetailModal(id) {
  document.getElementById('detailModal').classList.add('open');
  document.getElementById('detailModalBody').innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-light);">⏳ Memuat...</div>';
  
  // URL Fetch diarahkan menuju sub-endpoint AJAX terkelola di Controller pesanan
  fetch('<?= BASEURL; ?>admin/pesanan/detailAjax/' + id)
    .then(r => r.text())
    .then(html => { 
      document.getElementById('detailModalBody').innerHTML = html; 
    })
    .catch(() => { 
      document.getElementById('detailModalBody').innerHTML = '<div class="alert alert-danger">Gagal memuat detail.</div>'; 
    });
}
</script>
