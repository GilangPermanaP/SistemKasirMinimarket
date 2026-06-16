<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-tags"></i> Manajemen Kategori</span>
    </div>
    
    <div class="grid-cols-2">
        <div>
            <h3><?php echo isset($_GET['edit_id']) ? 'Edit Kategori' : 'Tambah Kategori'; ?></h3>
            <form action="index.php?page=kategori&action=<?php echo isset($_GET['edit_id']) ? 'edit' : 'create'; ?>" method="POST" style="margin-top: 1rem;">
                <?php if (isset($_GET['edit_id'])): 
                    $edit_cat = $this->model->getById('kategori', 'id_kategori', $_GET['edit_id']);
                ?>
                    <input type="hidden" name="id_kategori" value="<?php echo htmlspecialchars($edit_cat['id_kategori']); ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" class="form-input" value="<?php echo isset($edit_cat) ? htmlspecialchars($edit_cat['nama_kategori']) : ''; ?>" required autocomplete="off">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
                <?php if (isset($_GET['edit_id'])): ?>
                    <a href="index.php?page=kategori" class="btn btn-secondary"><i class="fa-solid fa-rotate-left"></i> Batal</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div>
            <h3>Daftar Kategori</h3>
            <div class="search-sort-bar" style="margin-top: 1rem;">
                <form action="index.php" method="GET" class="search-form">
                    <input type="hidden" name="page" value="kategori">
                    <input type="text" name="search" class="form-input" placeholder="Cari..." value="<?php echo htmlspecialchars($query); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="index.php?page=kategori&search=<?php echo urlencode($query); ?>&sort=nama_kategori&dir=<?php echo $order_by === 'nama_kategori' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Nama Kategori <i class="fa-solid <?php echo $order_by === 'nama_kategori' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                                </a>
                            </th>
                            <th style="width: 150px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kategori_list)): ?>
                            <tr>
                                <td colspan="2" style="text-align: center;">Tidak ada data.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kategori_list as $kategori): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($kategori['nama_kategori']); ?></td>
                                    <td style="text-align: center;">
                                        <a href="index.php?page=kategori&edit_id=<?php echo $kategori['id_kategori']; ?>&search=<?php echo urlencode($query); ?>" class="btn btn-warning btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="index.php?page=kategori&action=delete&id=<?php echo $kategori['id_kategori']; ?>" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Hapus kategori ini?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
