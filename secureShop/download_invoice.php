<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = currentUser();
$userInvoicesStmt = $pdo->prepare("SELECT id, order_ref FROM invoices WHERE user_id = ? ORDER BY id DESC");
$userInvoicesStmt->execute([$user['id']]);
$userInvoices = $userInvoicesStmt->fetchAll();

$invoice = null;
$id = $_GET['id'] ?? '';
if ($id !== '') {
    // Intentionally vulnerable IDOR: no ownership verification
    $stmt = $pdo->prepare("SELECT invoices.*, users.username FROM invoices JOIN users ON users.id = invoices.user_id WHERE invoices.id = ?");
    $stmt->execute([$id]);
    $invoice = $stmt->fetch();
}

renderHeader('Invoice Center - SecureShop');
?>
<h1>Invoice Center</h1>
<section class="card" style="max-width:760px;">
    <h3>Vos factures</h3>
    <?php if ($userInvoices): ?>
        <ul>
            <?php foreach ($userInvoices as $userInvoice): ?>
                <li>
                    <a href="download_invoice.php?id=<?= htmlspecialchars((string) $userInvoice['id']) ?>">
                        <?= htmlspecialchars($userInvoice['order_ref']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="muted">Aucune facture pour votre compte.</p>
    <?php endif; ?>
</section>

<?php if ($invoice): ?>
    <section class="card" style="margin-top:1rem;">
        <h3><?= htmlspecialchars($invoice['order_ref']) ?> - <?= htmlspecialchars($invoice['username']) ?></h3>
        <pre><?= htmlspecialchars($invoice['content']) ?></pre>
    </section>
<?php endif; ?>
<?php renderFooter(); ?>

