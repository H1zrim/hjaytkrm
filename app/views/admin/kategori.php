<div class="admin-page-header">
  <div>
    <h1>Kelola Kategori</h1>
    <p>Tambah, ubah, atau hapus kategori produk.</p>
  </div>
  <div class="header-actions">
    <button class="btn btn-primary" onclick="document.getElementById('modalTambah').classList.add('open')">
      ＋ Tambah Kategori
    </button>
  </div>
</div>

<?php if (isset($data['errors']) && !empty($data['errors'])): ?>
  <div class="alert alert-danger" style="margin-bottom: 16px;">
    <?php foreach ($data['errors'] as $e): ?>❌ <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
  </div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 340px;gap:18px;" class="kat-grid">
  
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">🏷️ Daftar Kategori</div>
      <span style="font-size:13px;color:var(--text-light);"><?= count($data['kategoriList'] ?? []) ?> kategori</span>
    </div>
    
    <?php if (empty($data['kategoriList'])): ?>
      <div class="empty-state">
        <div class="empty-icon">📂</div>
        <h3>Belum ada kategori</h3>
        <p>Tambahkan kategori pertama untuk mulai mengelola produk.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="tbl">
          <thead>
            <tr>
              <th>No</th><th>Icon</th><th>Nama Kategori</th>
              <th>Deskripsi</th><th>Produk</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data['kategoriList'] as $i => $k): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td style="font-size:22px;text-align:center;"><?= htmlspecialchars($k['icon']) ?></td>
                <td><strong><?= htmlspecialchars($k['nama_kategori']) ?></strong></td>
                <td style="color:var(--text-light);max-width:180px;">
                  <?= htmlspecialchars(mb_substr($k['deskripsi'], 0, 60)) ?><?= mb_strlen($k['deskripsi']) > 60 ? '…' : '' ?>
                </td>
                <td><span class="badge badge-gold"><?= (int)$k['jml_produk'] ?> produk</span></td>
                <td>
                  <div class="tbl-actions">
                    <a href="<?= BASEURL; ?>admin/kategori?edit=<?= $k['id'] ?>" class="btn btn-info btn-xs">✏️ Edit</a>
                    
                    <?php if ($k['jml_produk'] == 0): ?>
                      <button class="btn btn-danger btn-xs"
                              onclick="confirmDelete('<?= BASEURL; ?>admin/kategori/hapus/<?= $k['id'] ?>','Hapus kategori &quot;<?= htmlspecialchars($k['nama_kategori']) ?>&quot;?')">
                        🗑 Hapus
                      </button>
                    <?php else: ?>
                      <span class="btn btn-outline btn-xs" title="Tidak bisa dihapus, masih ada produk" style="opacity:.5;cursor:default;">🗑 Hapus</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <?php if (isset($data['editData']) && $data['editData']): ?>
    <div class="admin-card">
      <div class="admin-card-header">
        <div class="admin-card-title">✏️ Edit Kategori</div>
        <a href="<?= BASEURL; ?>admin/kategori" class="btn btn-ghost btn-sm">✕ Batal</a>
      </div>
      <form method="POST" action="<?= BASEURL; ?>admin/kategori/prosesEdit">
        <input type="hidden" name="id" value="<?= (int)$data['editData']['id'] ?>">
        
        <div class="form-group" style="margin-bottom:12px;">
          <label>Icon (Emoji)</label>
          <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($data['editData']['icon']) ?>" maxlength="4">
        </div>
        <div class="form-group" style="margin-bottom:12px;">
          <label>Nama Kategori</label>
          <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($data['editData']['nama_kategori']) ?>" required>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
          <label>Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($data['editData']['deskripsi']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success btn-block">💾 Simpan Perubahan</button>
      </form>
    </div>
  <?php else: ?>
    <div class="admin-card">
      <div class="admin-card-title" style="margin-bottom:14px;">💡 Panduan</div>
      <div style="font-size:13px;color:var(--text-mid);line-height:1.8;font-family:'Lora',serif;">
        <p>• Kategori digunakan untuk mengelompokkan produk di katalog.</p>
        <p style="margin-top:8px;">• Kategori yang memiliki produk <strong>tidak dapat dihapus</strong>.</p>
        <p style="margin-top:8px;">• Nama kategori bersifat unik, tidak boleh sama.</p>
        <p style="margin-top:8px;">• Gunakan emoji sebagai icon agar tampil menarik.</p>
      </div>
    </div>
  <?php endif; ?>
</div>

<div class="modal-overlay" id="modalTambah">
  <div class="modal-box">
    <div class="modal-header">
      <h3>➕ Tambah Kategori Baru</h3>
      <button class="modal-close" onclick="document.getElementById('modalTambah').classList.remove('open')">✕</button>
    </div>
    <form method="POST" action="<?= BASEURL; ?>admin/kategori/prosesTambah">
      <div class="modal-body">
        <div class="form-group" style="margin-bottom:12px;">
          <label>Icon (Emoji)</label>
          <input type="text" name="icon" class="form-control" value="📦" maxlength="4" placeholder="📦">
          <div class="form-hint">Gunakan satu emoji sebagai ikon kategori</div>
        </div>
        <div class="form-group" style="margin-bottom:12px;">
          <label>Nama Kategori *</label>
          <input type="text" name="nama_kategori" class="form-control" placeholder="cth: Kurma Madinah" required>
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi" class="form-control" placeholder="Deskripsi singkat kategori..." rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambah').classList.remove('open')">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Simpan Kategori</button>
      </div>
    </form>
  </div>
</div>

<style>@media(max-width:700px){.kat-grid{grid-template-columns:1fr!important;}}</style>