<?php
/**
 * ==============================================================================
 * MINI MVC LAYOUT - HEADER PELANGGAN (CUSTOMER)
 * ==============================================================================
 */
$navAktif = $data['nav_aktif'] ?? 'home';

// Hitung total quantity item di keranjang belanja
$cartCount = 0;
if (isset($_SESSION['pelanggan_id'])) {
    $cartItems = $_SESSION['cart'] ?? [];
    $cartCount = array_sum(array_column($cartItems, 'qty'));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?= isset($data['judul']) ? htmlspecialchars($data['judul']) : 'Haji Ayat Kurma'; ?> — Haji Ayat Kurma</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Path absolut menggunakan BASEURL -->
    <link rel="stylesheet" href="<?= BASEURL; ?>css/customer.css">
</head>
<body>

<div class="toast" id="toast"></div>

<!-- LOGOUT MODAL -->
<div class="modal-overlay" id="logoutModal">
  <div class="modal-box">
    <h3>Ingin Logout?</h3>
    <p>Anda akan keluar dari sesi ini. Keranjang belanja akan tetap tersimpan.</p>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('logoutModal')">Tidak</button>
      <!-- Mengarah ke AuthController method logout -->
      <a href="<?= BASEURL; ?>auth/logout" class="btn btn-danger">Ya, Keluar</a>
    </div>
  </div>
</div>

<div class="topbar">🐫 Gratis Ongkir pembelian di atas Rp 150.000 &nbsp;|&nbsp; Produk Original dari Madinah</div>

<nav class="cust-nav">
  <a href="<?= BASEURL; ?>" class="nav-logo">🌴 Haji <span>Ayat Kurma</span></a>
  
  <ul class="nav-links">
    <li><a href="<?= BASEURL; ?>" class="<?= $navAktif === 'home' ? 'active' : '' ?>">Beranda</a></li>
    <li><a href="<?= BASEURL; ?>catalog" class="<?= $navAktif === 'catalog' ? 'active' : '' ?>">Katalog</a></li>
    <li><a href="<?= BASEURL; ?>orders" class="<?= $navAktif === 'orders' ? 'active' : '' ?>">Pesanan Saya</a></li>
    <li><a href="<?= BASEURL; ?>profile" class="<?= $navAktif === 'profile' ? 'active' : '' ?>">Profil</a></li>
  </ul>
  
  <div class="nav-actions">
    <a href="<?= BASEURL; ?>cart" class="nav-cart">
      🛒 Keranjang <span class="cart-badge" id="cartBadge"><?= $cartCount ?></span>
    </a>
    
    <?php if (isset($_SESSION['pelanggan_id'])): ?>
      <button class="nav-user-btn" onclick="openModal('logoutModal')">
        👤 <?= htmlspecialchars($_SESSION['pelanggan_nama'] ?? 'Saya') ?>
      </button>
    <?php else: ?>
      <!-- Jika belum login, arahkan ke halaman login pelanggan -->
      <a href="<?= BASEURL; ?>login" class="nav-user-btn" style="text-decoration: none; text-align: center;">
        🔑 Login
      </a>
    <?php endif; ?>
    
    <button class="hamburger-cust" id="hamburgerBtn" onclick="toggleMobileNav()">☰</button>
  </div>
</nav>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
  <a href="<?= BASEURL; ?>" class="<?= $navAktif === 'home' ? 'active' : '' ?>">🏠 Beranda</a>
  <a href="<?= BASEURL; ?>catalog" class="<?= $navAktif === 'catalog' ? 'active' : '' ?>">🌴 Katalog</a>
  <a href="<?= BASEURL; ?>cart">🛒 Keranjang (<?= $cartCount ?>)</a>
  <a href="<?= BASEURL; ?>orders" class="<?= $navAktif === 'orders' ? 'active' : '' ?>">📦 Pesanan Saya</a>
  <a href="<?= BASEURL; ?>profile" class="<?= $navAktif === 'profile' ? 'active' : '' ?>">👤 Profil</a>
  
  <?php if (isset($_SESSION['pelanggan_id'])): ?>
    <a href="#" onclick="openModal('logoutModal'); toggleMobileNav();" style="color:#c0392b;">🚪 Keluar</a>
  <?php else: ?>
    <a href="<?= BASEURL; ?>login" style="color:#27ae60;">🔑 Masuk / Login</a>
  <?php endif; ?>
</div>

<script>
function toggleMobileNav(){
  document.getElementById('mobileNav').classList.toggle('open');
}
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }

document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.modal-overlay').forEach(m=>{
    m.addEventListener('click',e=>{ if(e.target===m) m.classList.remove('open'); });
  });
  
  // auto-close flash
  setTimeout(()=>{
    document.querySelectorAll('.alert').forEach(a=>{
      a.style.transition='opacity .4s'; a.style.opacity='0';
      setTimeout(()=>a.remove(),400);
    });
  },4000);
});

function showToast(msg){
  const t=document.getElementById('toast');
  t.textContent=msg; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),3000);
}
</script>