<?php
// app/public/api/history.php
header('Content-Type: application/json; charset=utf-8');

// Читаем из Docker env или fallback
$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'poll_app';
$user = getenv('DB_USER') ?: 'poll_user';
$pass = getenv('DB_PASS') ?: '';

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // последние 20 попыток, от новых к старым
    $sql = "SELECT id, username, score, answers, created_at
            FROM attempts
            ORDER BY created_at DESC
            LIMIT 20";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    echo json_encode([
        'ok'    => true,
        'items' => $rows,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB error: ' . $e->getMessage()]);
}
