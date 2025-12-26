<?php
// app/public/api/submit.php
header('Content-Type: application/json; charset=utf-8');

// Читаем из Docker env или fallback
$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'poll_app';
$user = getenv('DB_USER') ?: 'poll_user';
$pass = getenv('DB_PASS') ?: '';

// читаем JSON-тело
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['username'], $input['score'], $input['answers'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Bad payload']);
    exit;
}

$username = trim((string)$input['username']);
$score    = (int)$input['score'];
$answers  = $input['answers'];

if ($username === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Empty username']);
    exit;
}

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

    $sql = "INSERT INTO attempts (username, score, answers)
            VALUES (:u, :s, :a::jsonb)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':u' => $username,
        ':s' => $score,
        ':a' => json_encode($answers, JSON_UNESCAPED_UNICODE),
    ]);

    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB error: ' . $e->getMessage()]);
}
