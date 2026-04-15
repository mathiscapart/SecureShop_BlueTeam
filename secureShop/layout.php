<?php
function renderHeader(string $title): void
{
    $user = currentUser();
    ?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        :root { --primary:#3b82f6; --accent:#22d3ee; --bg:#f8fafc; --text:#0f172a; --card:#fff; }
        [data-theme="dark"] { --bg:#0b1020; --text:#e2e8f0; --card:#161d31; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:Arial,sans-serif; background:var(--bg); color:var(--text); }
        header { background:linear-gradient(135deg,#3b82f6,#22d3ee); padding:1rem 2rem; position:sticky; top:0; }
        nav { max-width:1200px; margin:auto; display:flex; justify-content:space-between; align-items:center; }
        nav a { color:#fff; text-decoration:none; margin-right:1rem; font-weight:700; }
        .container { max-width:1200px; margin:auto; padding:2rem; }
        .card { background:var(--card); border-radius:12px; padding:1.2rem; box-shadow:0 8px 24px rgba(0,0,0,.1); }
        .btn { background:linear-gradient(135deg,#3b82f6,#22d3ee); color:#fff; border:none; padding:.6rem 1rem; border-radius:10px; cursor:pointer; text-decoration:none; display:inline-block; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1rem; }
        .product-image { width:100%; height:220px; object-fit:contain; background:#fff; border-radius:8px; }
        input, textarea { width:100%; padding:.7rem; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:1rem; }
        .muted { opacity:.8; font-size:.95rem; }
        footer { text-align:center; padding:2rem; margin-top:2rem; }
    </style>
</head>
<body>
<header>
    <nav>
        <div>
            <a href="index.php">SecureShop</a>
            <a href="products.php">Products</a>
            <a href="search.php">Search</a>
            <?php if ($user): ?>
                <a href="download_invoice.php">Invoice Center</a>
                <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                    <a href="upload.php">Upload</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($user): ?>
                <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout (<?= htmlspecialchars($user['username']) ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            <button class="btn" onclick="toggleTheme()">Theme</button>
        </div>
    </nav>
</header>
<main class="container">
    <?php
}

function renderFooter(): void
{
    ?>
</main>
<footer>
    <div>SecureShop - Deliberately Vulnerable Training Lab</div>
</footer>
<script>
const saved = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', saved);
function toggleTheme() {
  const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);
}
</script>
</body>
</html>
<?php
}

