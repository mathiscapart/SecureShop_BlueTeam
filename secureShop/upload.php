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

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Intentionally unsafe: no extension/mime validation
    $target = $uploadDir . '/' . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        $msg = 'Uploaded to: uploads/' . basename($_FILES['file']['name']);
    } else {
        $msg = 'Upload failed.';
    }
}

renderHeader('File Upload - SecureShop');
?>
<section class="card" style="max-width:760px;">
    <h1>Support File Upload</h1>
    <p class="muted">Upload any file for order troubleshooting.</p>
    <?php if ($msg): ?><p><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button class="btn" type="submit">Upload</button>
    </form>
</section>
<?php renderFooter(); ?>

