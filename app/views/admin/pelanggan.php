
<div class="admin-page-header">
  <div>
    <h1>Data Pelanggan</h1>
    <p>Daftar seluruh pelanggan yang terdaftar di toko.</p>
  </div>
</div>

<form method="GET" action="<?= BASEURL; ?>admin/pelanggan" class="filter-bar">
  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" name="q" class="form-control" placeholder="Cari nama, email, atau no HP..." 
           value="<?= htmlspecialchars($data['search'] ?? '') ?>">
  </div>
  <button type="submit" class="btn btn-outline">Cari</button>
  
  <?php if (!empty($data['search'])): ?>
    <a href="<?= BASEURL; ?>admin/pelanggan" class="btn btn-ghost">✕</a>
  <?php endif; ?>
</form>

<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">👥 Daftar Pelanggan</div>
    <span style="font-size:13px;color:var(--text-light);"><?= count($data['pelangganList'] ?? []) ?> pelanggan</span>
  </div>
  
  <?php if (empty($data['pelangganList'])): ?>
    <div class="empty-state">
      <div class="empty-icon">👤</div>
      <h3>Belum ada pelanggan</h3>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="tbl">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Kota</th>
            <th>Total Pesanan</th>
            <th>Total Belanja</th>
            <th>Daftar</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data['pelangganList'] as $i => $pl): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:8px;">
                  <div style="width:32px;height:32px;background:var(--sand-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">👤</div>
                  <div>
                    <div style="font-weight:700;font-size:13px;"><?= htmlspecialchars($pl['nama_pengguna']) ?></div>
                    <div style="font-size:11px;color:var(--text-light);"><?= htmlspecialchars($pl['nama_penerima'] ?? '') ?></div>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($pl['email']) ?></td>
              <td><?= htmlspecialchars($pl['no_hp'] ?: '-') ?></td>
              <td style="font-size:12px;color:var(--text-light);">
                <?= htmlspecialchars($pl['kota'] ?? 'Samarinda') ?>
              </td>
              <td><span class="badge badge-gold"><?= (int)$pl['total_pesanan'] ?>×</span></td>
              <td><strong>Rp <?= number_format($pl['total_belanja'], 0, ',', '.'); ?></strong></td>
              <td style="font-size:12px;color:var(--text-light);">
                <?= date('d M Y', strtotime($pl['created_at'])) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
