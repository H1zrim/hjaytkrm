<?php
$this->view('/layouts/header-admin', $data);
$this->view('/layouts/sidebar-admin', $data);
?>

<div class="admin-page-header">
  <div>
    <h1>Konfirmasi Pembayaran</h1>
    <p>Verifikasi bukti pembayaran dan konfirmasi pesanan yang sudah diproses.</p>
  </div>
</div>

<div class="alert alert-info" style="margin-bottom:16px;">
  ℹ️ Halaman ini menampilkan pesanan berstatus <strong>Diproses</strong>. Setelah memeriksa mutasi rekening, klik <strong>Konfirmasi Pembayaran</strong> untuk mengubah status menjadi <strong>Lunas</strong>.
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:18px;" class="konfbay-grid">

  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">💳 Pesanan Menunggu Verifikasi</div>
      <span style="font-size:13px;color:var(--text-light);"><?= count($data['pesananList'] ?? []) ?> pesanan</span>
    </div>

    <form method="GET" action="<?= BASEURL; ?>admin/pembayaran" class="filter-bar" style="margin-bottom:12px;">
      <div class="search-wrap">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" class="form-control" placeholder="Cari invoice / pelanggan..." 
               value="<?= htmlspecialchars($data['search'] ?? '') ?>">
      </div>
      <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    </form>

    <?php if (empty($data['pesananList'])): ?>
      <div class="empty-state" style="padding:40px 0;">
        <div class="empty-icon">✅</div>
        <h3>Tidak ada pesanan menunggu</h3>
        <p>Semua pembayaran sudah terverifikasi.</p>
      </div>
    <?php else: ?>
      <?php foreach ($data['pesananList'] as $p): ?>
        <?php 
        // Mengecek apakah baris pesanan ini sedang aktif dipilih
        $isActive = isset($data['activeId']) && $data['activeId'] == $p['id']; 
        
        // Mempertahankan query parameter pencarian saat baris diklik
        $querySearch = !empty($data['search']) ? '&q=' . urlencode($data['search']) : '';
        ?>
        <div style="
          border:1px solid <?= $isActive ? 'var(--accent-gold)' : 'rgba(201,169,110,.2)' ?>;
          border-radius:10px;padding:14px;margin-bottom:10px;
          background:<?= $isActive ? 'var(--sand-lightest)' : 'white' ?>;
          cursor:pointer;transition:all .2s;
        " onclick="window.location='<?= BASEURL; ?>admin/pembayaran?id=<?= $p['id'] . $querySearch ?>'">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
            <div>
              <div style="font-weight:700;font-size:14px;color:var(--brown-dark);"><?= htmlspecialchars($p['invoice']) ?></div>
              <div style="font-size:12px;color:var(--text-light);margin-top:2px;">
                <?= htmlspecialchars($p['nama_pengguna']) ?> · <?= date('d M Y', strtotime($p['created_at'])) ?>
              </div>
              <div style="font-size:12px;margin-top:4px;">
                <?php 
                $met = ['qris' => '📱 QRIS', 'transfer' => '🏦 Transfer', 'cod' => '💵 COD']; 
                echo htmlspecialchars($met[$p['metode_bayar']] ?? $p['metode_bayar']);
                ?>
                <?php if ($p['bukti_bayar']): ?>
                  · <span style="color:#27ae60;font-weight:700;">📎 Ada Bukti</span>
                <?php else: ?>
                  · <span style="color:var(--accent-warm);">📭 Belum ada bukti</span>
                <?php endif; ?>
              </div>
            </div>
            <div style="text-align:right;">
              <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--brown-dark);">
                Rp <?= number_format($p['total'], 0, ',', '.'); ?>
              </div>
              <?php if ($isActive): ?>
                <span style="font-size:11px;color:var(--accent-gold);font-weight:700;">▶ Dilihat</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div>
    <?php if (isset($data['detailPesanan']) && $data['detailPesanan']): ?>
      <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">📋 <?= htmlspecialchars($data['detailPesanan']['invoice']) ?></div>
          <span class="badge badge-processed">Diproses</span>
        </div>

        <div style="margin-bottom:16px;">
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);margin-bottom:8px;">Bukti Pembayaran</div>
          <?php 
          // Path validasi dipindahkan ke logika pengecekan di controller, di view murni mengecek ketersediaan data path gambar
          if (!empty($data['detailPesanan']['bukti_bayar'])): 
          ?>
            <div class="bukti-wrap">
              <img src="<?= BASEURL . htmlspecialchars($data['detailPesanan']['bukti_bayar']) ?>" alt="Bukti Bayar">
              <div style="margin-top:8px;font-size:12px;color:var(--text-light);">
                <a href="<?= BASEURL . htmlspecialchars($data['detailPesanan']['bukti_bayar']) ?>" target="_blank" class="btn btn-outline btn-xs">🔍 Perbesar</a>
              </div>
            </div>
          <?php else: ?>
            <div class="bukti-wrap">
              <div class="no-bukti">📭 Pelanggan belum mengunggah bukti pembayaran.</div>
            </div>
          <?php endif; ?>
        </div>

        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);margin-bottom:8px;">Detail Produk</div>
        <?php foreach (($data['detailItems'] ?? []) as $item): ?>
          <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid rgba(201,169,110,.1);">
            <span style="font-size:20px;"><?= htmlspecialchars($item['icon'] ?? '📦') ?></span>
            <div style="flex:1;">
              <div style="font-size:13px;font-weight:600;"><?= htmlspecialchars($item['nama_produk']) ?></div>
              <div style="font-size:11px;color:var(--text-light);"><?= htmlspecialchars($item['satuan'] ?? '') ?> × <?= (int)$item['qty'] ?></div>
            </div>
            <strong style="font-size:13px;">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></strong>
          </div>
        <?php endforeach; ?>

        <div style="background:var(--sand-lightest);border-radius:8px;padding:12px;margin:12px 0;font-size:13px;">
          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
            <span style="color:var(--text-light);">Subtotal</span>
            <strong>Rp <?= number_format($data['detailPesanan']['subtotal'], 0, ',', '.'); ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
            <span style="color:var(--text-light);">Ongkir</span>
            <strong><?= $data['detailPesanan']['ongkir'] == 0 ? 'GRATIS' : 'Rp ' . number_format($data['detailPesanan']['ongkir'], 0, ',', '.'); ?></strong>
          </div>
          <?php if ($data['detailPesanan']['diskon'] > 0): ?>
            <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
              <span style="color:var(--text-light);">Diskon</span>
              <strong style="color:#27ae60;">- Rp <?= number_format($data['detailPesanan']['diskon'], 0, ',', '.'); ?></strong>
            </div>
          <?php endif; ?>
          <div style="display:flex;justify-content:space-between;border-top:1px solid var(--border-sand);padding-top:8px;margin-top:4px;">
            <strong style="font-size:14px;">Total</strong>
            <strong style="font-family:'Playfair Display',serif;font-size:17px;color:var(--accent-warm);">
              Rp <?= number_format($data['detailPesanan']['total'], 0, ',', '.'); ?>
            </strong>
          </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:8px;">
          <a href="<?= BASEURL; ?>admin/pembayaran/konfirmasi/<?= $data['detailPesanan']['id'] ?>"
             class="btn btn-success btn-block"
             onclick="return confirm('Konfirmasi pembayaran pesanan <?= htmlspecialchars($data['detailPesanan']['invoice']) ?>? Pastikan sudah cek mutasi rekening.')">
            ✅ Konfirmasi Pembayaran
          </a>
          <button class="btn btn-danger btn-block"
                  onclick="confirmDelete('<?= BASEURL; ?>admin/pembayaran/tolak/<?= $data['detailPesanan']['id'] ?>','Tolak pembayaran dan batalkan pesanan <?= htmlspecialchars($data['detailPesanan']['invoice']) ?>?')">
            ❌ Tolak (Batalkan Pesanan)
          </button>
        </div>
      </div>
    <?php else: ?>
      <div class="admin-card" style="text-align:center;padding:40px 20px;">
        <div style="font-size:48px;margin-bottom:12px;">👈</div>
        <div style="font-family:'Playfair Display',serif;font-size:16px;color:var(--brown-dark);margin-bottom:6px;">Pilih pesanan</div>
        <div style="font-size:13px;color:var(--text-light);font-family:'Lora',serif;">Klik salah satu pesanan di kiri untuk melihat detail dan bukti pembayaran.</div>
      </div>
    <?php endif; ?>
  </div>

</div>

<style>@media(max-width:700px){.konfbay-grid{grid-template-columns:1fr!important;}}</style>

<?php
$this->view('/layouts/footer-admin', $data);
?>