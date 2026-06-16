<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir Minimarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php if (isset($_SESSION['user'])): ?>
<div class="app-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-cart-shopping"></i>
            <h2>Kasir Market</h2>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php?page=dashboard" class="<?php echo ($_GET['page'] ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
            </li>
            <?php if ($_SESSION['user']['role'] === 'Admin'): ?>
            <li>
                <a href="index.php?page=kategori" class="<?php echo ($_GET['page'] ?? '') === 'kategori' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-tags"></i> Kategori
                </a>
            </li>
            <li>
                <a href="index.php?page=barang" class="<?php echo ($_GET['page'] ?? '') === 'barang' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box"></i> Barang (Master)
                </a>
            </li>
            <li>
                <a href="index.php?page=member" class="<?php echo ($_GET['page'] ?? '') === 'member' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> Member (Master)
                </a>
            </li>
            <li>
                <a href="index.php?page=user" class="<?php echo ($_GET['page'] ?? '') === 'user' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-gear"></i> User (Master)
                </a>
            </li>
            <li>
                <a href="index.php?page=laporan" class="<?php echo ($_GET['page'] ?? '') === 'laporan' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Laporan
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="index.php?page=transaksi" class="<?php echo ($_GET['page'] ?? '') === 'transaksi' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-cash-register"></i> Transaksi Kasir
                </a>
            </li>
            <li>
                <a href="index.php?page=logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </li>
            <li>
                <a href="index.php?page=logout&exit=true">
                    <i class="fa-solid fa-circle-xmark"></i> Exit
                </a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <div class="user-profile">
                <i class="fa-solid fa-user-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['user']['username']); ?> (<?php echo htmlspecialchars($_SESSION['user']['role']); ?>)</span>
            </div>
        </div>
        <div class="content-body">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?>">
                    <?php 
                    echo $_SESSION['flash']['message']; 
                    unset($_SESSION['flash']);
                    ?>
                </div>
            <?php endif; ?>
<?php endif; ?>
