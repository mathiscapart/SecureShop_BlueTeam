<?php
session_start();

function renderRuntimeErrorPage(string $title, string $message): void
{
    if (headers_sent() === false) {
        http_response_code(500);
    }
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
    echo <<<HTML
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$safeTitle}</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #0f172a; color: #e2e8f0; }
        .wrap { max-width: 900px; margin: 48px auto; padding: 0 16px; }
        .card { background: #1e293b; border-radius: 12px; padding: 20px; border: 1px solid #334155; }
        h1 { margin-top: 0; color: #f87171; }
        .msg { background: #111827; padding: 12px; border-radius: 8px; line-height: 1.5; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>{$safeTitle}</h1>
            <div class="msg">{$safeMessage}</div>
        </div>
    </div>
</body>
</html>
HTML;
    exit;
}

set_error_handler(static function (
    int $severity,
    string $message,
    string $file,
    int $line
): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    renderRuntimeErrorPage('Erreur PHP', "{$message}\nFichier: {$file}\nLigne: {$line}");
});

set_exception_handler(static function (Throwable $exception): void {
    renderRuntimeErrorPage(
        'Exception non gérée',
        $exception->getMessage() . "\nFichier: " . $exception->getFile() . "\nLigne: " . $exception->getLine()
    );
});

register_shutdown_function(static function (): void {
    $lastError = error_get_last();
    if ($lastError === null) {
        return;
    }
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (in_array($lastError['type'], $fatalTypes, true)) {
        renderRuntimeErrorPage(
            'Erreur fatale',
            $lastError['message'] . "\nFichier: " . $lastError['file'] . "\nLigne: " . $lastError['line']
        );
    }
});

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'secureshop';
$dbUser = getenv('DB_USER') ?: 'secureshop';
$dbPass = getenv('DB_PASS') ?: 'securepass';

$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    renderRuntimeErrorPage('Erreur base de données', $e->getMessage());
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

