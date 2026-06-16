<?php
$this->view('/layouts/header-pelanggan', $data);
?>

<div class="page-header">
  <div class="breadcrumb"><a href="<?= BASEURL; ?>">Beranda</a> › Profil Saya</div>
  <h1>Profil Saya</h1>
</div>

<?php if (isset($data['errors']) && !empty($data['errors'])): ?>
  <div style="padding:0 32px;">
    <div class="alert alert-danger">
      <?php foreach ($data['errors'] as $e): ?>❌ <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<div class="profile-layout">
  <div class="profile-sidebar">
    <div class="p-avatar">👤</div>
    <div class="p-name"><?= htmlspecialchars($data['pelanggan']['nama_pengguna']) ?></div>
    <div class="p-email"><?= htmlspecialchars($data['pelanggan']['email']) ?></div>

    <div style="display:flex;gap:8px;margin-bottom:16px;">
      <div style="flex:1;background:var(--sand-lightest);border-radius:8px;padding:10px;text-align:center;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--brown-dark);">
          <?= (int)($data['totalPesanan'] ?? 0) ?>
        </div>
        <div style="font-size:10px;color:var(--text-light);">Pesanan</div>
      </div>
      <div style="flex:1;background:var(--sand-lightest);border-radius:8px;padding:10px;text-align:center;">
        <div style="font-size:12px;font-weight:700;color:var(--brown-dark);">
          Rp <?= number_format($data['totalBelanja'] ?? 0, 0, ',', '.'); ?>
        </div>
        <div style="font-size:10px;color:var(--text-light);">Belanja</div>
      </div>
    </div>

    <?php $activeTab = $data['activeTab'] ?? 'profil'; ?>
    <ul class="p-menu">
      <li class="<?= $activeTab === 'profil' ? 'active' : '' ?>" onclick="window.location='<?= BASEURL; ?>profile?tab=profil'">📋 Data Diri</li>
      <li onclick="window.location='<?= BASEURL; ?>orders'">📦 Pesanan Saya</li>
      <li class="<?= $activeTab === 'password' ? 'active' : '' ?>" onclick="window.location='<?= BASEURL; ?>profile?tab=password'">🔒 Ubah Kata Sandi</li>
      <li onclick="openModal('logoutModal')" style="color:#c0392b;margin-top:14px;">🚪 Keluar</li>
    </ul>
  </div>

  <div class="profile-content">
    <?php if ($activeTab === 'profil'): ?>
      <h2>Data Diri</h2>
      <form method="POST" action="<?= BASEURL; ?>profile/update">
        <div class="form-row">
          <div class="form-group">
            <label>Nama Pengguna *</label>
            <input type="text" name="nama_pengguna" class="form-control"
                   value="<?= htmlspecialchars($data['pelanggan']['nama_pengguna']) ?>" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($data['pelanggan']['email']) ?>" disabled
                   style="background:var(--sand-lightest);cursor:not-allowed;">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>No. Handphone</label>
            <input type="tel" name="no_hp" class="form-control"
                   value="<?= htmlspecialchars($data['pelanggan']['no_hp'] ?? '') ?>" placeholder="0812-xxxx-xxxx">
          </div>
          <div class="form-group">
            <label>Nama Penerima Paket</label>
            <input type="text" name="nama_penerima" class="form-control"
                   value="<?= htmlspecialchars($data['pelanggan']['nama_penerima'] ?? '') ?>" placeholder="Nama penerima">
          </div>
        </div>
        <div class="form-group">
          <label>Alamat Pengiriman Utama</label>
          <input type="text" name="alamat" class="form-control"
                 value="<?= htmlspecialchars($data['pelanggan']['alamat'] ?? '') ?>"
                 placeholder="Jl. nama jalan, no. rumah, RT/RW, kelurahan, kota">
        </div>
        <div class="form-group">
          <label>Kode Pos</label>
          <input type="text" name="kode_pos" class="form-control" style="max-width:160px;"
                 value="<?= htmlspecialchars($data['pelanggan']['kode_pos'] ?? '') ?>" placeholder="75xxx">
        </div>
        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
      </form>

    <?php elseif ($activeTab === 'password'): ?>
      <h2>Ubah Kata Sandi</h2>
      <form method="POST" action="<?= BASEURL; ?>profile/changepassword" style="max-width:400px;">
        <div class="form-group">
          <label>Kata Sandi Lama *</label>
          <input type="password" name="password_lama" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="form-group">
          <label>Kata Sandi Baru *</label>
          <input type="password" name="password_baru" class="form-control" placeholder="Min. 6 karakter" required>
        </div>
        <div class="form-group">
          <label>Konfirmasi Kata Sandi Baru *</label>
          <input type="password" name="konfirmasi_baru" class="form-control" placeholder="Ulangi kata sandi baru" required>
        </div>
        <button type="submit" class="btn btn-primary">🔒 Ubah Kata Sandi</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php
$this->view('/layouts/footer-pelanggan', $data);
?>