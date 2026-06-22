<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-house"></i> Dashboard</span>
        <span>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</span>
    </div>
    <p>Sistem Kasir Minimarket - Proyek Tugas Kuliah atas nama Abdul Zabar</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Barang (Master)</h3>
            <p><?php echo number_format($total_barang); ?></p>
        </div>
        <div class="stat-icon bg-blue-light">
            <i class="fa-solid fa-box"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Member (Master)</h3>
            <p><?php echo number_format($total_member); ?></p>
        </div>
        <div class="stat-icon bg-green-light">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Transaksi Kasir</h3>
            <p><?php echo number_format($total_transaksi); ?></p>
        </div>
        <div class="stat-icon bg-yellow-light">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Pendapatan</h3>
            <p>Rp <?php echo number_format($total_revenue, 2, ',', '.'); ?></p>
        </div>
        <div class="stat-icon bg-purple-light">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-compass"></i> Quick Actions</span>
    </div>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="index.php?page=transaksi" class="btn btn-primary">
            <i class="fa-solid fa-cash-register"></i> Buka Kasir
        </a>
        <?php if ($_SESSION['user']['role'] === 'Admin'): ?>
        <a href="index.php?page=barang" class="btn btn-success">
            <i class="fa-solid fa-box-open"></i> Kelola Barang
        </a>
        <a href="index.php?page=member" class="btn btn-warning">
            <i class="fa-solid fa-user-plus"></i> Kelola Member
        </a>
        <a href="index.php?page=laporan" class="btn btn-secondary">
            <i class="fa-solid fa-print"></i> Lihat Laporan
        </a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
