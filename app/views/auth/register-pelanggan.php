<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Registrasi — Haji Ayat Kurma</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASEURL; ?>css/customer.css">
</head>
<body>
<div class="auth-wrap">
  <div style="background:var(--brown-bg);padding:16px 24px;">
    <a href="<?= BASEURL; ?>" class="nav-logo" style="color:var(--sand-lightest);">🌴 Haji <span>Ayat Kurma</span></a>
  </div>

  <div class="auth-body" style="padding:24px 16px;">
    <div class="auth-card" style="max-width:480px;">
      <h2>Registrasi Akun Baru</h2>

      <?php if (class_exists('Flasher')) { Flasher::flash(); } ?>

      <form method="POST" action="<?= BASEURL; ?>login/prosesRegister" novalidate>

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
          <div class="form-group">
            <label>No. Handphone</label>
            <input type="tel" name="no_hp" class="form-control" placeholder="0812-xxxx-xxxx"
                   value="<?= htmlspecialchars($data['input']['no_hp'] ?? '') ?>">
          </div>
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

        <div class="form-section" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);margin:14px 0 10px;">Data Penerima</div>

        <div class="form-group">
          <label>Nama Penerima</label>
          <input type="text" name="nama_penerima" class="form-control" placeholder="Nama penerima paket"
                 value="<?= htmlspecialchars($data['input']['nama_penerima'] ?? '') ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Kode Pos</label>
            <input type="text" name="kode_pos" class="form-control" placeholder="75xxx"
                   value="<?= htmlspecialchars($data['input']['kode_pos'] ?? '') ?>">
          </div>
          <div class="form-group"></div>
        </div>

        <div class="form-group">
          <label>Alamat Pengiriman</label>
          <input type="text" name="alamat" class="form-control"
                 placeholder="Jl. nama jalan, no. rumah, RT/RW, kelurahan, kota"
                 value="<?= htmlspecialchars($data['input']['alamat'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top:6px;">Daftar Sekarang</button>
      </form>

      <div class="auth-alt" style="margin-top:14px;">
        Sudah punya akun? <a href="<?= BASEURL; ?>login">Login di sini</a>
      </div>
    </div>
  </div>

  <div style="background:var(--brown-bg);padding:10px;text-align:center;font-size:11px;color:rgba(201,169,110,.35);font-family:'Lora',serif;">
    @HjAyatKurma2026
  </div>
</div>
</body>
</html>
