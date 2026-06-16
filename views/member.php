<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-users"></i> Manajemen Member</span>
    </div>

    <div class="grid-cols-2">
        <div>
            <h3><?php echo isset($_GET['edit_id']) ? 'Edit Member' : 'Tambah Member'; ?></h3>
            <form action="index.php?page=member&action=<?php echo isset($_GET['edit_id']) ? 'edit' : 'create'; ?>" method="POST" style="margin-top: 1rem;">
                <?php if (isset($_GET['edit_id'])): 
                    $edit_member = $this->model->getById('member', 'id_member', $_GET['edit_id']);
                ?>
                    <input type="hidden" name="id_member" value="<?php echo htmlspecialchars($edit_member['id_member']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nama_member">Nama Member</label>
                    <input type="text" name="nama_member" id="nama_member" class="form-input" value="<?php echo isset($edit_member) ? htmlspecialchars($edit_member['nama_member']) : ''; ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="kode_member">Kode Member</label>
                    <input type="text" name="kode_member" id="kode_member" class="form-input" value="<?php echo isset($edit_member) ? htmlspecialchars($edit_member['kode_member']) : ''; ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="diskon_persen">Diskon (%)</label>
                    <input type="number" step="0.01" name="diskon_persen" id="diskon_persen" class="form-input" min="0" max="100" value="<?php echo isset($edit_member) ? htmlspecialchars($edit_member['diskon_persen']) : '0'; ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
                <?php if (isset($_GET['edit_id'])): ?>
                    <a href="index.php?page=member" class="btn btn-secondary"><i class="fa-solid fa-rotate-left"></i> Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div>
            <h3>Daftar Member</h3>
            <div class="search-sort-bar" style="margin-top: 1rem;">
                <form action="index.php" method="GET" class="search-form">
                    <input type="hidden" name="page" value="member">
                    <input type="text" name="search" class="form-input" placeholder="Cari member..." value="<?php echo htmlspecialchars($query); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="index.php?page=member&search=<?php echo urlencode($query); ?>&sort=nama_member&dir=<?php echo $order_by === 'nama_member' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Nama <i class="fa-solid <?php echo $order_by === 'nama_member' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?page=member&search=<?php echo urlencode($query); ?>&sort=kode_member&dir=<?php echo $order_by === 'kode_member' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Kode <i class="fa-solid <?php echo $order_by === 'kode_member' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?page=member&search=<?php echo urlencode($query); ?>&sort=diskon_persen&dir=<?php echo $order_by === 'diskon_persen' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Diskon <i class="fa-solid <?php echo $order_by === 'diskon_persen' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                                </a>
                            </th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($member_list)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">Tidak ada data member.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($member_list as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['nama_member']); ?></td>
                                    <td><code><?php echo htmlspecialchars($member['kode_member']); ?></code></td>
                                    <td><?php echo htmlspecialchars($member['diskon_persen']); ?>%</td>
                                    <td style="text-align: center;">
                                        <a href="index.php?page=member&edit_id=<?php echo $member['id_member']; ?>&search=<?php echo urlencode($query); ?>" class="btn btn-warning btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="index.php?page=member&action=delete&id=<?php echo $member['id_member']; ?>" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Hapus member ini?');">
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
