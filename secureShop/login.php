<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Intentionally vulnerable SQL for training purpose
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' ORDER BY (role = 'admin') DESC, id ASC LIMIT 1";
    $result = $pdo->query($sql)->fetch();

    if ($result) {
        $_SESSION['user'] = $result;
        header('Location: ' . ($result['role'] === 'admin' ? 'admin.php' : 'index.php'));
        exit;
    }
    $error = 'Invalid credentials';
}

renderHeader('Login - SecureShop');
?>
<section class="card" style="max-width:560px;margin:auto;">
    <h1>Login</h1>
    <?php if ($error): ?><p><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button class="btn" type="submit">Login</button>
    </form>
    <p class="muted">Test accounts: alice/password, bob/password</p>
    <p class="muted">Hint: legacy SQL handling still exists.</p>
</section>
<script src="assets/flag-celebration.js"></script>
<?php renderFooter(); ?>

