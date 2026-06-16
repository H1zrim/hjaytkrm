<?php
$this->view('/layouts/header-pelanggan', $data);
?>

<div style="padding:40px 16px;">
  <div class="success-wrap">
    <div class="success-icon">🎉</div>
    <h2>Pesanan Berhasil Dibuat!</h2>
    <p>Terima kasih telah berbelanja di Haji Ayat Kurma. Pesanan Anda sedang kami proses.</p>
    
    <p style="font-size:13px;color:var(--text-light);">
      No. Pesanan: <strong style="color:var(--brown-dark);"><?= htmlspecialchars($data['pesanan']['invoice']) ?></strong>
    </p>

    <div class="pay-info-card">
      <?php 
      $metode = $data['pesanan']['metode_bayar'] ?? 'transfer'; 
      ?>
      
      <?php if ($metode === 'qris'): ?>
        <h4>📱 Pembayaran via QRIS</h4>
        <div style="width:160px;height:160px;background:white;border:2px solid var(--border-sand);border-radius:8px;margin:14px auto;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:4px;">
          <span style="font-size:48px;">⬛</span>
          <span style="font-size:10px;color:var(--text-light);">QRIS Haji Ayat Kurma</span>
        </div>
        <div class="pay-info-row">
          <span>Total Bayar</span>
          <span class="v">Rp <?= number_format($data['pesanan']['total'], 0, ',', '.'); ?></span>
        </div>
        <div class="pay-info-row">
          <span>No. Pesanan</span>
          <span class="v"><?= htmlspecialchars($data['pesanan']['invoice']) ?></span>
        </div>
        <p style="margin-top:10px;font-size:11px;color:var(--text-light);font-family:'Lora',serif;font-style:italic;">
          Scan QR di atas menggunakan aplikasi bank atau dompet digital Anda
        </p>

      <?php elseif ($metode === 'transfer'): ?>
        <h4>🏦 Transfer Bank</h4>
        <div class="pay-info-row"><span>Bank</span><span class="v">BCA</span></div>
        <div class="pay-info-row"><span>No. Rekening</span><span class="v">1234 5678 90</span></div>
        <div class="pay-info-row"><span>Atas Nama</span><span class="v">Haji Ayat Kurma</span></div>
        <div style="height:1px;background:var(--border-sand);margin:10px 0;"></div>
        <div class="pay-info-row">
          <span>Total Transfer</span>
          <span class="v" style="color:var(--accent-warm);">Rp <?= number_format($data['pesanan']['total'], 0, ',', '.'); ?></span>
        </div>
        <p style="margin-top:10px;font-size:11px;color:var(--text-light);font-family:'Lora',serif;font-style:italic;">
          Upload bukti transfer di halaman "Pesanan Saya" setelah melakukan transfer
        </p>

      <?php else: ?>
        <h4>💵 Bayar di Tempat (COD)</h4>
        <div class="pay-info-row">
          <span>Total Tagihan</span>
          <span class="v">Rp <?= number_format($data['pesanan']['total'], 0, ',', '.'); ?></span>
        </div>
        <p style="margin-top:10px;font-size:11px;color:var(--text-light);font-family:'Lora',serif;font-style:italic;">
          Siapkan uang pas saat kurir tiba. Pembayaran dilakukan langsung kepada kurir.
        </p>
      <?php endif; ?>
    </div>

    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="<?= BASEURL; ?>orders" class="btn btn-primary btn-lg">📦 Lihat Pesanan Saya</a>
      <a href="<?= BASEURL; ?>catalog" class="btn btn-outline btn-lg">🌴 Lanjut Belanja</a>
    </div>
  </div>
</div>

<?php
$this->view('/layouts/footer-pelanggan', $data);
?>