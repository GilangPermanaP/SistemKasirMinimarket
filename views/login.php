<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="login-body">
    <div class="login-container">
        <div class="login-header">
            <i class="fa-solid fa-store"></i>
            <h1>Minimarket Kasir</h1>
            <p>Please login to your account</p>
        </div>
        
        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="index.php?page=login" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-input" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-input" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
