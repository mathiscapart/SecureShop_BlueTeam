<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = currentUser();
if (($user['role'] ?? 'user') !== 'admin') {
    header('Location: index.php');
    exit;
}

renderHeader('Admin Panel - SecureShop');
?>
<section class="card">
    <h1>Admin Panel</h1>
    <p>Welcome <?= htmlspecialchars($user['username']) ?>.</p>
    <p class="muted">If you reached this page through SQL injection, challenge solved.</p>
    <p><strong>Flag:</strong> THM{sql_injection_admin_access}</p>
</section>
<?php renderFooter(); ?>

