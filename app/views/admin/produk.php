
<div class="admin-page-header">
  <div>
    <h1>Kelola Produk</h1>
    <p>Tambah, edit, dan hapus data produk toko.</p>
  </div>
  <div class="header-actions">
    <button class="btn btn-primary" onclick="openModal('modalTambah')">＋ Tambah Produk</button>
  </div>
</div>

<?php if (isset($data['errors']) && !empty($data['errors'])): ?>
  <div class="alert alert-danger" style="margin-bottom: 16px;">
    <?php foreach ($data['errors'] as $e): ?>❌ <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="GET" action="<?= BASEURL; ?>admin/produk" class="filter-bar">
  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" name="q" class="form-control" placeholder="Cari nama produk..." value="<?= htmlspecialchars($data['search'] ?? '') ?>">
  </div>
  <select name="kat" class="filter-select" onchange="this.form.submit()">
    <option value="">Semua Kategori</option>
    <?php foreach (($data['kategoriAll'] ?? []) as $k): ?>
      <option value="<?= $k['id'] ?>" <?= (isset($data['katFilter']) && $data['katFilter'] == $k['id']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($k['nama_kategori']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-outline">Filter</button>
  
  <?php if (!empty($data['search']) || !empty($data['katFilter'])): ?>
    <a href="<?= BASEURL; ?>admin/produk" class="btn btn-ghost">✕ Reset</a>
  <?php endif; ?>
</form>

<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">🌴 Daftar Produk</div>
    <span style="font-size:13px;color:var(--text-light);"><?= count($data['produkList'] ?? []) ?> produk</span>
  </div>
  
  <?php if (empty($data['produkList'])): ?>
    <div class="empty-state">
      <div class="empty-icon">📦</div>
      <h3>Belum ada produk</h3>
      <p>Produk yang terdaftar akan muncul di sini.</p>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="tbl">
        <thead>
          <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Satuan</th>
            <th>Badge</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data['produkList'] as $i => $p): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td style="text-align:center;"><?= produk_img($p, '44px', '6px') ?></td>
              <td>
                <strong><?= htmlspecialchars($p['nama']) ?></strong>
                <?php if (!empty($p['deskripsi'])): ?>
                  <div style="font-size:11px;color:var(--text-light);margin-top:2px;">
                    <?= htmlspecialchars(mb_substr($p['deskripsi'], 0, 50)) ?>…
                  </div>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></td>
              <td><strong>Rp <?= number_format($p['harga'], 0, ',', '.'); ?></strong></td>
              <td>
                <span class="badge <?= $p['stok'] <= 5 ? 'badge-cancelled' : ($p['stok'] <= 10 ? 'badge-pending' : 'badge-ok') ?>">
                  <?= (int)$p['stok'] ?>
                </span>
              </td>
              <td><?= htmlspecialchars($p['satuan']) ?></td>
              <td><?= $p['badge'] ? '<span class="badge badge-gold">' . htmlspecialchars($p['badge']) . '</span>' : '-' ?></td>
              <td>
                <div class="tbl-actions">
                  <button class="btn btn-info btn-xs"
                          onclick="openEditModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)">✏️ Edit</button>
                  
                  <button class="btn btn-danger btn-xs"
                          onclick="confirmDelete('<?= BASEURL; ?>admin/produk/hapus/<?= $p['id'] ?>','Hapus produk &quot;<?= htmlspecialchars($p['nama']) ?>&quot;?')">🗑 Hapus</button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="modal-overlay" id="modalTambah">
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <h3>➕ Tambah Produk Baru</h3>
      <button class="modal-close" onclick="closeModal('modalTambah')">✕</button>
    </div>
    <form method="POST" action="<?= BASEURL; ?>admin/produk/prosesTambah" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group form-full">
            <label>Nama Produk *</label>
            <input type="text" name="nama" class="form-control" placeholder="cth: Kurma Ajwa Premium" required>
          </div>
          <div class="form-group form-full">
            <label>Foto Produk (JPG/PNG/WEBP, maks 2MB)</label>
            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp"
                   onchange="previewFoto(this,'prevTambah')">
            <img id="prevTambah" src="" alt="" style="display:none;margin-top:8px;width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border-sand);">
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" class="form-control">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach (($data['kategoriAll'] ?? []) as $k): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['icon'] . ' ' . $k['nama_kategori']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Harga (Rp) *</label>
            <input type="number" name="harga" class="form-control" placeholder="125000" min="0" required>
          </div>
          <div class="form-group">
            <label>Stok Awal</label>
            <input type="number" name="stok" class="form-control" value="0" min="0">
          </div>
          <div class="form-group">
            <label>Satuan</label>
            <input type="text" name="satuan" class="form-control" value="500g" placeholder="cth: 500g, 1L, pcs">
          </div>
          <div class="form-group">
            <label>Icon (Emoji)</label>
            <input type="text" name="icon" class="form-control" value="📦" maxlength="4">
          </div>
          <div class="form-group">
            <label>Badge (opsional)</label>
            <input type="text" name="badge" class="form-control" placeholder="cth: Best Seller, Baru">
          </div>
          <div class="form-group form-full">
            <label>Deskripsi Produk</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi lengkap produk..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalTambah')">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Simpan Produk</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modalEdit">
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <h3>✏️ Edit Produk</h3>
      <button class="modal-close" onclick="closeModal('modalEdit')">✕</button>
    </div>
    <form method="POST" action="<?= BASEURL; ?>admin/produk/prosesEdit" id="formEdit" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id">
      <input type="hidden" name="foto_lama" id="edit_foto_lama">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group form-full">
            <label>Nama Produk *</label>
            <input type="text" name="nama" id="edit_nama" class="form-control" required>
          </div>
          <div class="form-group form-full">
            <label>Foto Produk (kosongkan jika tidak ingin mengganti)</label>
            <div style="display:flex;align-items:center;gap:12px;">
              <img id="prevEdit" src="" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid var(--border-sand);">
              <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp"
                     onchange="previewFoto(this,'prevEdit')">
            </div>
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" id="edit_kategori" class="form-control">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach (($data['kategoriAll'] ?? []) as $k): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['icon'] . ' ' . $k['nama_kategori']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Harga (Rp) *</label>
            <input type="number" name="harga" id="edit_harga" class="form-control" min="0" required>
          </div>
          <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" id="edit_stok" class="form-control" min="0">
          </div>
          <div class="form-group">
            <label>Satuan</label>
            <input type="text" name="satuan" id="edit_satuan" class="form-control">
          </div>
          <div class="form-group">
            <label>Icon (Emoji)</label>
            <input type="text" name="icon" id="edit_icon" class="form-control" maxlength="4">
          </div>
          <div class="form-group">
            <label>Badge</label>
            <input type="text" name="badge" id="edit_badge" class="form-control">
          </div>
          <div class="form-group form-full">
            <label>Deskripsi</label>
            <textarea name="deskripsi" id="edit_desk" class="form-control" rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
        <button type="submit" class="btn btn-success">💾 Update Produk</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function openEditModal(p){
  document.getElementById('edit_id').value = p.id;
  document.getElementById('edit_nama').value = p.nama;
  document.getElementById('edit_harga').value = p.harga;
  document.getElementById('edit_stok').value = p.stok;
  document.getElementById('edit_satuan').value = p.satuan;
  document.getElementById('edit_icon').value = p.icon;
  document.getElementById('edit_badge').value = p.badge;
  document.getElementById('edit_desk').value = p.deskripsi;
  document.getElementById('edit_foto_lama').value = p.foto || '';
  const prev = document.getElementById('prevEdit');
  if (p.foto) {
    prev.src = '<?= BASEURL ?>uploads/produk/' + p.foto;
    prev.style.display = 'block';
  } else {
    prev.src = ''; prev.style.display = 'none';
  }
  const sel = document.getElementById('edit_kategori');
  for(let o of sel.options){ if(o.value == p.kategori_id){ o.selected=true; break; } }
  openModal('modalEdit');
}
function previewFoto(input, targetId) {
  const prev = document.getElementById(targetId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { prev.src = e.target.result; prev.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

