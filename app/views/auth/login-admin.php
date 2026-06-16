<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Login Admin — Haji Ayat Kurma</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= BASEURL; ?>css/admin.css">
    
    <style>
        body{background:var(--brown-bg);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}
        .login-wrap{width:100%;max-width:420px;}
        .login-brand{text-align:center;margin-bottom:28px;}
        .login-brand .logo{font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:var(--sand-lightest);}
        .login-brand .logo span{color:var(--accent-gold);}
        .login-brand .sub{font-size:12px;color:rgba(201,169,110,.5);text-transform:uppercase;letter-spacing:2px;margin-top:4px;}
        .login-card{background:var(--white-warm);border:1px solid var(--border-sand);border-radius:16px;padding:36px 32px;box-shadow:0 20px 60px rgba(0,0,0,.3);}
        .login-card h2{font-family:'Playfair Display',serif;font-size:22px;color:var(--brown-dark);margin-bottom:4px;text-align:center;}
        .login-card .subtitle{text-align:center;font-size:13px;color:var(--text-light);margin-bottom:24px;font-family:'Lora',serif;}
        .login-footer{text-align:center;margin-top:20px;font-size:12px;color:rgba(201,169,110,.4);}
        .login-footer a{color:rgba(201,169,110,.6);}
        .demo-box{background:rgba(201,169,110,.06);border:1px dashed rgba(201,169,110,.3);border-radius:8px;padding:12px 14px;margin-top:16px;font-size:12px;color:rgba(201,169,110,.7);line-height:1.7;}
        .demo-box strong{color:var(--accent-gold);}
    </style>
</head>
<body>
<div class="login-wrap">
  <div class="login-brand">
    <div class="logo">🌴 Haji <span>Ayat Kurma</span></div>
    <div class="sub">Admin Panel</div>
  </div>
  <div class="login-card">
    <h2>Login Admin</h2>
    <p class="subtitle">Masuk ke panel pengelolaan toko 🛒</p>

    <?php if (isset($data['error']) && !empty($data['error'])): ?>
      <div class="alert alert-danger">❌ <?= htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <?php 
      if (class_exists('Flasher')) {
          Flasher::flash();
      }
    ?>
    <form action="<?= BASEURL; ?>loginadmin/prosesLogin" method="POST" novalidate>
      <div class="form-group" style="margin-bottom:14px;">
        <label>Email Admin</label>
        <input type="email" name="email" class="form-control"
               placeholder="admin@ayatkurma.com"
               value="<?= htmlspecialchars($data['email'] ?? ''); ?>" required>
      </div>
      <div class="form-group" style="margin-bottom:18px;">
        <label>Kata Sandi</label>
        <input type="password" name="password" class="form-control"
               placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary btn-lg btn-block">Masuk ke Dashboard →</button>
    </form>
  </div>
  <div class="login-footer">
    <a href="<?= BASEURL; ?>">← Kembali ke Toko</a> &nbsp;|&nbsp; © 2026 Haji Ayat Kurma
  </div>
</div>
</body>
</html>