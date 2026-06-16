<div class="container-fluid mt-4">
    <h3>Edit Produk</h3>
    <div class="card shadow p-4">
        <form action="<?= BASEURL; ?>produk/prosesEdit" method="POST">
            <input type="hidden" name="id" value="<?= $data['produk']['id']; ?>">
            
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" value="<?= $data['produk']['nama_produk']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Harga</label>
                <input type="number" name="harga" class="form-control" value="<?= $data['produk']['harga']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stok" class="form-control" value="<?= $data['produk']['stok']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= $data['produk']['deskripsi']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Update Produk</button>
            <a href="<?= BASEURL; ?>produk" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>