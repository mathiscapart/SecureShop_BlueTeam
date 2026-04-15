<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

$q = $_GET['q'] ?? '';
$results = [];
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like]);
    $results = $stmt->fetchAll();
}

renderHeader('Search - SecureShop');
?>
<h1>Search Products</h1>
<section class="card">
    <form method="get" style="display:flex;gap:.7rem;">
        <input type="text" name="q" placeholder="Search..." value="<?= $q ?>" required>
        <button class="btn" type="submit">Search</button>
    </form>
</section>

<?php if ($q !== ''): ?>
    <section class="card" style="margin-top:1rem;">
        <h2>Search results for: <?= $q ?></h2>
        <p class="muted">This output is intentionally unsanitized for XSS training.</p>
        <div class="grid">
            <?php foreach ($results as $product): ?>
                <article class="card">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php else: ?>
    <section class="card" style="margin-top:1rem;">
        <h2>XSS Challenge</h2>
        <p>Try reflected payloads to read <code>window.xssFlag</code>.</p>
    </section>
<?php endif; ?>

<script src="assets/flag-celebration.js"></script>
<script>
Object.defineProperty(window, 'xssFlag', {
  get() { showFlagCelebration('THM{reflected_xss_2025}', 'XSS', 'Reflected XSS exploited!'); return 'THM{reflected_xss_2025}'; }
});
</script>
<?php renderFooter(); ?>

