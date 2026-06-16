<div class="container-fluid mt-4">
    <h3>Tambah Produk Baru</h3>
    <div class="mb-3">
    <label>Kategori</label>
    <select name="id_kategori" class="form-control" required>
        <?php foreach($data['kategori'] as $k): ?>
            <option value="<?= $k['id']; ?>"><?= $k['nama_kategori']; ?></option>
        <?php endforeach; ?>
    </select>
</div>
    <div class="card shadow p-4">
        <form action="<?= BASEURL; ?>produk/prosesTambah" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Harga</label>
                <input type="number" name="harga" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stok" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
            <a href="<?= BASEURL; ?>produk" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>