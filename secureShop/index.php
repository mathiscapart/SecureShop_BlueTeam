<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

$products = $pdo->query("SELECT * FROM products LIMIT 6")->fetchAll();

renderHeader('SecureShop - Gaming & Tech Store');
?>
<section class="card" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:center;">
    <div>
        <h1>Welcome to SecureShop</h1>
        <p class="muted">Your destination for gaming gear and cybersecurity training labs.</p>
        <a class="btn" href="products.php">Shop Now</a>
    </div>
    <img src="assets/Welcome.jpg" alt="Welcome" style="width:100%;border-radius:12px;">
</section>

<h2>Featured Products</h2>
<section class="grid">
    <?php foreach ($products as $product): ?>
        <article class="card">
            <img class="product-image" src="<?= htmlspecialchars('assets/' . basename($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p class="muted"><?= htmlspecialchars($product['description']) ?></p>
            <strong>$<?= number_format((float) $product['price'], 2) ?></strong>
        </article>
    <?php endforeach; ?>
</section>

<!-- CTF Discovery Hint: Check out download_invoice.php -->
<?php renderFooter(); ?>

