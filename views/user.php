<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-user-gear"></i> Manajemen User</span>
    </div>

    <div class="grid-cols-2">
        <div>
            <h3><?php echo isset($_GET['edit_id']) ? 'Edit User' : 'Tambah User'; ?></h3>
            <form action="index.php?page=user&action=<?php echo isset($_GET['edit_id']) ? 'edit' : 'create'; ?>" method="POST" style="margin-top: 1rem;">
                <?php if (isset($_GET['edit_id'])): 
                    $edit_user = $this->model->getById('user', 'id_user', $_GET['edit_id']);
                ?>
                    <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($edit_user['id_user']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-input" value="<?php echo isset($edit_user) ? htmlspecialchars($edit_user['username']) : ''; ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Password <?php echo isset($_GET['edit_id']) ? '(Biarkan kosong jika tidak diubah)' : ''; ?></label>
                    <input type="password" name="password" id="password" class="form-input" <?php echo isset($_GET['edit_id']) ? '' : 'required'; ?>>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-input" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="Admin" <?php echo (isset($edit_user) && $edit_user['role'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="Operator" <?php echo (isset($edit_user) && $edit_user['role'] === 'Operator') ? 'selected' : ''; ?>>Operator</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
                <?php if (isset($_GET['edit_id'])): ?>
                    <a href="index.php?page=user" class="btn btn-secondary"><i class="fa-solid fa-rotate-left"></i> Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div>
            <h3>Daftar User</h3>
            <div class="search-sort-bar" style="margin-top: 1rem;">
                <form action="index.php" method="GET" class="search-form">
                    <input type="hidden" name="page" value="user">
                    <input type="text" name="search" class="form-input" placeholder="Cari user..." value="<?php echo htmlspecialchars($query); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="index.php?page=user&search=<?php echo urlencode($query); ?>&sort=username&dir=<?php echo $order_by === 'username' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Username <i class="fa-solid <?php echo $order_by === 'username' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                                </a>
                            </th>
                            <th>
                                <a href="index.php?page=user&search=<?php echo urlencode($query); ?>&sort=role&dir=<?php echo $order_by === 'role' && $direction === 'ASC' ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Role <i class="fa-solid <?php echo $order_by === 'role' ? ($direction === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'; ?>"></i>
                                </a>
                            </th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($user_list)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">Tidak ada data user.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($user_list as $usr): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usr['username']); ?></td>
                                    <td>
                                        <span class="btn btn-secondary" style="padding: 0.1rem 0.5rem; font-size: 0.8rem; pointer-events: none; background-color: <?php echo $usr['role'] === 'Admin' ? '#6366f1' : '#64748b'; ?>;">
                                            <?php echo htmlspecialchars($usr['role']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="index.php?page=user&edit_id=<?php echo $usr['id_user']; ?>&search=<?php echo urlencode($query); ?>" class="btn btn-warning btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="index.php?page=user&action=delete&id=<?php echo $usr['id_user']; ?>" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Hapus user ini?');">
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
