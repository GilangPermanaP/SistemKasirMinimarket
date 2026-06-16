<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-box"></i> Manajemen Barang (Master)</span>
        <?php if ($action !== 'add' && $action !== 'edit_view'): ?>
            <a href="index.php?page=barang&action=add" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Barang
            </a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'add'): ?>
        <h3>Tambah Barang Baru</h3>
        <form action="index.php?page=barang&action=create" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem; max-width: 600px;">
            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" name="nama_barang" id="nama_barang" class="form-input" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="id_kategori">Kategori</label>
                <select name="id_kategori" id="id_kategori" class="form-input" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori_list as $kategori): ?>
                        <option value="<?php echo $kategori['id_kategori']; ?>"><?php echo htmlspecialchars($kategori['nama_kategori']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" step="0.01" name="harga" id="harga" class="form-input" min="0" required>
            </div>

            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" name="stok" id="stok" class="form-input" min="0" required>
            </div>

            <div class="form-group">
                <label for="foto_barang">Foto Barang</label>
                <input type="file" name="foto_barang" id="foto_barang" class="form-input" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan
            </button>
            <a href="index.php?page=barang" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Batal
            </a>
        </form>

    <?php elseif ($action === 'edit_view'): ?>
        <h3>Edit Barang</h3>
        <form action="index.php?page=barang&action=edit" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem; max-width: 600px;">
            <input type="hidden" name="id_barang" value="<?php echo htmlspecialchars($barang['id_barang']); ?>">

            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" name="nama_barang" id="nama_barang" class="form-input" value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="id_kategori">Kategori</label>
                <select name="id_kategori" id="id_kategori" class="form-input" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori_list as $kategori): ?>
                        <option value="<?php echo $kategori['id_kategori']; ?>" <?php echo $kategori['id_kategori'] == $barang['id_kategori'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" step="0.01" name="harga" id="harga" class="form-input" value="<?php echo htmlspecialchars($barang['harga']); ?>" min="0" required>
            </div>

            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" name="stok" id="stok" class="form-input" value="<?php echo htmlspecialchars($barang['stok']); ?>" min="0" required>
            </div>

            <div class="form-group">
                <label for="foto_barang">Foto Barang (Biarkan kosong jika tidak ingin diubah)</label>
                <input type="file" name="foto_barang" id="foto_barang" class="form-input" accept="image/*">
                <div style="margin-top: 0.5rem;">
                    <img src="assets/images/<?php echo $barang['foto_barang']; ?>" class="product-img" alt="Foto">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
            <a href="index.php?page=barang" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Batal
            </a>
        </form>

    <?php else: ?>
        <div class="search-sort-bar">
            <form action="index.php" method="GET" class="search-form">
                <input type="hidden" name="page" value="barang">
                <input type="text" name="search" class="form-input" placeholder="Cari barang..." value="<?php echo htmlspecialchars($query); ?>">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Foto</th>
                        <th>
                            <a href="index.php?page=barang&search=<?php echo urlencode($query); ?>&sort=nama_barang&dir=<?php echo $order_by === 'nama_barang' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                Nama Barang <i class="fa-solid <?php echo $order_by === 'nama_barang' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                            </a>
                        </th>
                        <th>
                            <a href="index.php?page=barang&search=<?php echo urlencode($query); ?>&sort=kategori.nama_kategori&dir=<?php echo $order_by === 'kategori.nama_kategori' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                Kategori <i class="fa-solid <?php echo $order_by === 'kategori.nama_kategori' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                            </a>
                        </th>
                        <th>
                            <a href="index.php?page=barang&search=<?php echo urlencode($query); ?>&sort=harga&dir=<?php echo $order_by === 'harga' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                Harga <i class="fa-solid <?php echo $order_by === 'harga' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                            </a>
                        </th>
                        <th>
                            <a href="index.php?page=barang&search=<?php echo urlencode($query); ?>&sort=stok&dir=<?php echo $order_by === 'stok' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                Stok <i class="fa-solid <?php echo $order_by === 'stok' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                            </a>
                        </th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barang_list)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada data barang.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($barang_list as $row): ?>
                            <tr>
                                <td>
                                    <img src="assets/images/<?php echo $row['foto_barang']; ?>" class="product-img" alt="Foto">
                                </td>
                                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                                <td>Rp <?php echo number_format($row['harga'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($row['stok']); ?></td>
                                <td style="text-align: center;">
                                    <a href="index.php?page=barang&action=edit_view&id=<?php echo $row['id_barang']; ?>" class="btn btn-warning btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="index.php?page=barang&action=delete&id=<?php echo $row['id_barang']; ?>" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Hapus barang ini?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
