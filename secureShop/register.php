<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $email, $password]); // intentionally plain password storage
        $newUserId = (int) $pdo->lastInsertId();

        $invoiceRef = 'INV-' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        $invoiceContent = "Invoice {$invoiceRef}\nCustomer: {$username}\nItems: Welcome Pack\nTotal: $0.00";
        $invoiceStmt = $pdo->prepare("INSERT INTO invoices (user_id, order_ref, content) VALUES (?, ?, ?)");
        $invoiceStmt->execute([$newUserId, $invoiceRef, $invoiceContent]);

        $message = 'Account created. You can login now.';
    } catch (Throwable $e) {
        $message = 'Registration failed: ' . $e->getMessage();
    }
}

renderHeader('Register - SecureShop');
?>
<section class="card" style="max-width:560px;margin:auto;">
    <h1>Create account</h1>
    <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button class="btn" type="submit">Create Account</button>
    </form>
</section>
<?php renderFooter(); ?>

