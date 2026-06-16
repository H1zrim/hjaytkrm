<header>
  <title><?= $data['pageTitle'] ?? 'Haji Ayat Kurma'; ?></title>
  <link rel="stylesheet" href="<?= BASEURL; ?>css/style.css">
  <link rel="stylesheet" href="<?= BASEURL; ?>css/pelanggan.css">
</header>
<div class="auth-wrap">
  <div style="background:var(--brown-bg);padding:16px 24px;">
    <a href="<?= BASEURL; ?>" class="nav-logo" style="color:var(--sand-lightest);">🌴 Haji <span>Ayat Kurma</span></a>
  </div>

  <div class="auth-body" style="padding:24px 16px;">
    <div class="auth-card" style="max-width:480px;">
      <h2>Registrasi Akun Baru</h2>

      <?php 
        if (class_exists('Flasher')) {
            Flasher::flash();
        }
      ?>

      <?php if (isset($data['errors']) && !empty($data['errors'])): ?>
        <div class="alert alert-danger">
          <?php foreach ($data['errors'] as $err): ?>
            ❌ <?= htmlspecialchars($err) ?><br>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= BASEURL; ?>auth/prosesRegister" novalidate>
        
        <div class="form-group">
          <label>Nama Pengguna *</label>
          <input type="text" name="nama_pengguna" class="form-control" placeholder="Nama lengkap Anda"
                 value="<?= htmlspecialchars($data['input']['nama_pengguna'] ?? '') ?>" required>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" class="form-control" placeholder="nama@email.com"
                   value="<?= htmlspecialchars($data['input']['email'] ?? '') ?>" required>
          </div>
          <div class="form-group" style="opacity:0;pointer-events:none;"></div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Kata Sandi *</label>
            <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
          </div>
          <div class="form-group">
            <label>Konfirmasi Kata Sandi *</label>
            <input type="password" name="konfirmasi" class="form-control" placeholder="Ulangi kata sandi" required>
          </div>
        </div>

        <div class="form-section">Data Penerima</div>

        <div class="form-group">
          <label>Nama Penerima</label>
          <input type="text" name="nama_penerima" class="form-control" placeholder="Nama penerima paket"
                 value="<?= htmlspecialchars($data['input']['nama_penerima'] ?? '') ?>">
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>No. Handphone</label>
            <input type="tel" name="no_hp" class="form-control" placeholder="0812-xxxx-xxxx"
                   value="<?= htmlspecialchars($data['input']['no_hp'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Kode Pos</label>
            <input type="text" name="kode_pos" class="form-control" placeholder="75xxx"
                   value="<?= htmlspecialchars($data['input']['kode_pos'] ?? '') ?>">
          </div>
        </div>
        
        <div class="form-group">
          <label>Alamat Pengiriman</label>
          <input type="text" name="alamat" class="form-control"
                 placeholder="Jl. nama jalan, no. rumah, RT/RW, kelurahan, kota"
                 value="<?= htmlspecialchars($data['input']['alamat'] ?? '') ?>">
        </div>

        <div class="checkbox-row" style="margin-bottom:14px;">
          <input type="checkbox" id="setuju" name="setuju" <?= isset($data['input']['setuju']) ? 'checked' : '' ?>>
          <label for="setuju">Saya menyetujui <a href="#" style="color:var(--accent-warm);">syarat dan ketentuan</a></label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Registrasi</button>
      </form>

      <div class="auth-alt" style="margin-top:14px;">
        Sudah punya akun? <a href="<?= BASEURL; ?>auth/login" style="text-decoration:none;">Login di sini</a>
      </div>
    </div>
  </div>

  <div style="background:var(--brown-bg);padding:10px;text-align:center;font-size:11px;color:rgba(201,169,110,.35);font-family:'Lora',serif;">
    @HjAyatKurma2026
  </div>
</div>
<?php
$this->view('/layouts/footer-pelanggan', $data);
?>