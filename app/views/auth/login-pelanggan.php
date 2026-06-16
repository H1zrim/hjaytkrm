<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Login — Haji Ayat Kurma</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Perubahan: Menggunakan BASEURL agar CSS customer tidak pecah -->
    <link rel="stylesheet" href="<?= BASEURL; ?>css/customer.css">
</head>
<body>
<div class="auth-wrap">
  <!-- Top brand bar -->
  <div style="background:var(--brown-bg);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;">
    <a href="<?= BASEURL; ?>" class="nav-logo" style="color:var(--sand-lightest);">🌴 Haji <span>Ayat Kurma</span></a>
  </div>

  <div class="auth-body">
    <div class="auth-card">
      <h2>Login untuk Masuk</h2>
      <p class="auth-subtitle">Selamat datang kembali 🌙</p>

      <!-- Mengambil pesan error dari Controller -->
      <?php if (isset($data['error']) && !empty($data['error'])): ?>
        <div class="alert alert-danger">❌ <?= htmlspecialchars($data['error']); ?></div>
      <?php endif; ?>

      <!-- Mengambil Flash Message jika ada sistem Flasher core -->
      <?php 
        if (class_exists('Flasher')) {
            Flasher::flash(); 
        }
      ?>

      <form method="POST" action="<?= BASEURL; ?>login/prosesLogin" novalidate>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="nama@email.com"
                 value="<?= htmlspecialchars($data['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label>Kata Sandi</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="checkbox-row" style="margin-bottom:14px;">
          <input type="checkbox" id="ingat" name="ingat">
          <label for="ingat">Ingat Aku</label>
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">Masuk</button>
      </form>

      <div class="auth-alt" style="margin-top:16px;">
        Belum punya akun? <a href="<?= BASEURL; ?>login/register">Registrasi</a>
      </div>
      <div class="auth-alt" style="margin-top:8px;">
        <a href="#" style="color:var(--text-light);font-size:12px;">Aku melupakan sandiku</a>
      </div>
    </div>
  </div>

  <div style="background:var(--brown-bg);padding:10px;text-align:center;font-size:11px;color:rgba(201,169,110,.35);font-family:'Lora',serif;">
    @HjAyatKurma2026
  </div>
</div>
</body>
</html>