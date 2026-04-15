<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

$products = $pdo->query("SELECT * FROM products ORDER BY category, name")->fetchAll();
$byCategory = [];
foreach ($products as $product) {
    $byCategory[$product['category']][] = $product;
}

renderHeader('Products - SecureShop');
?>
<h1>Our Products</h1>
<?php foreach ($byCategory as $category => $items): ?>
    <h2><?= htmlspecialchars($category) ?></h2>
    <section class="grid">
        <?php foreach ($items as $product): ?>
            <article class="card">
                <img class="product-image" src="<?= htmlspecialchars('assets/' . basename($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p class="muted"><?= htmlspecialchars($product['description']) ?></p>
                <strong>$<?= number_format((float) $product['price'], 2) ?></strong>
            </article>
        <?php endforeach; ?>
    </section>
<?php endforeach; ?>

<div class="card">
    <h3>Customer invoices</h3>
    <p class="muted">Need your receipt? Visit <a href="download_invoice.php">download_invoice.php</a>.</p>
</div>
<?php renderFooter(); ?>

