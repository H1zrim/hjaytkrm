<?php

function cekNavAktif($currentKey, $activeKey) {
    return ($currentKey === $activeKey) ? 'active' : '';
}

// Pastikan variabel halaman_aktif aman dibaca
$halamanAktif = $data['halaman_aktif'] ?? '';
?>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">🌴 Haji <span>Ayat Kurma</span></div>
    <div class="brand-sub">Admin Panel</div>
  </div>
  
  <div class="sidebar-admin">
    <div class="admin-avatar">👤</div>
    <div class="admin-info">
      <div class="name"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></div>
      <div class="role">Super Admin</div>
    </div>
  </div>
  
  <nav class="sidebar-nav">
    <div class="nav-section-label">Utama</div>
    <a href="<?= BASEURL; ?>admin" class="nav-item <?= cekNavAktif('dashboard', $halamanAktif) ?>">
      <span class="icon">📊</span> Dashboard
    </a>

    <div class="nav-section-label">Katalog</div>
    <a href="<?= BASEURL; ?>admin/kategori" class="nav-item <?= cekNavAktif('kategori', $halamanAktif) ?>">
      <span class="icon">🏷️</span> Kategori
    </a>
    <a href="<?= BASEURL; ?>admin/produk" class="nav-item <?= cekNavAktif('produk', $halamanAktif) ?>">
      <span class="icon">🌴</span> Produk
    </a>
    <a href="<?= BASEURL; ?>admin/stok" class="nav-item <?= cekNavAktif('stok', $halamanAktif) ?>">
      <span class="icon">📦</span> Kelola Stok
    </a>

    <div class="nav-section-label">Transaksi</div>
    <a href="<?= BASEURL; ?>admin/pesanan" class="nav-item <?= cekNavAktif('pesanan', $halamanAktif) ?>">
      <span class="icon">🛒</span> Pesanan
      <?php if (!empty($data['badge_pending']) && $data['badge_pending'] > 0): ?>
          <span class="nav-badge"><?= $data['badge_pending']; ?></span>
      <?php endif; ?>
    </a>
    <a href="<?= BASEURL; ?>admin/pembayaran" class="nav-item <?= cekNavAktif('pembayaran', $halamanAktif) ?>">
      <span class="icon">💳</span> Konfirmasi Bayar
      <?php if (!empty($data['badge_konfirmasi']) && $data['badge_konfirmasi'] > 0): ?>
          <span class="nav-badge"><?= $data['badge_konfirmasi']; ?></span>
      <?php endif; ?>
    </a>

    <div class="nav-section-label">Pengguna</div>
    <a href="<?= BASEURL; ?>admin/pelanggan" class="nav-item <?= cekNavAktif('pelanggan', $halamanAktif) ?>">
      <span class="icon">👥</span> Data Pelanggan
    </a>
  </nav>
  
  <div class="sidebar-footer">
    <button class="logout-btn" onclick="document.getElementById('logoutModal').classList.add('open')">
      🚪 Keluar dari Panel
    </button>
  </div>
</aside>