<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    
    <title><?= isset($data['judul']) ? htmlspecialchars($data['judul']) : 'Dashboard'; ?> — Admin Haji Ayat Kurma</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= BASEURL; ?>css/admin.css">
</head>
<body>
<div class="admin-layout">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="main-content">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="hamburger" onclick="toggleSidebar()" title="Menu">☰</button>
                <div class="page-breadcrumb">
                    <?php if (!empty($data['breadcrumb'])): ?>
                        <?php foreach ($data['breadcrumb'] as $i => $b): ?>
                            <?php if ($i > 0) echo ' › '; ?>
                            <?php if (isset($b['url'])): ?>
                                <a href="<?= htmlspecialchars($b['url']) ?>"><?= htmlspecialchars($b['label']) ?></a>
                            <?php else: ?>
                                <strong><?= htmlspecialchars($b['label']) ?></strong>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <strong><?= isset($data['judul']) ? htmlspecialchars($data['judul']) : 'Dashboard'; ?></strong>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="topbar-right">
                <a href="<?= BASEURL; ?>" class="btn btn-outline btn-sm" target="_blank">🌐 Lihat Toko</a>
                
                <button class="topbar-user" onclick="document.getElementById('logoutModal').classList.add('open')">
                    👤 <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?> ▾
                </button>
            </div>
        </header>
        
        <main class="page-body">
            <?php 
                if (class_exists('Flasher')) {
                    Flasher::flash(); 
                }
            ?>