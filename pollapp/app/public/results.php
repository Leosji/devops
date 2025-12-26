<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$dsn  = "pgsql:host=127.0.0.1;port=5432;dbname=poll_app";
$user = getenv('DB_USER') ?: 'poll_user';
$pass = getenv('DB_PASS') ?: '';
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

    $sql = "SELECT id, username, score, answers, created_at
            FROM attempts
            ORDER BY created_at DESC
            LIMIT 50";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(); // вернет массив строк [web:59][web:183]
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>Ошибка подключения к БД</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Результаты опроса</title>
  <style>
    body { font-family: sans-serif; max-width: 900px; margin: 20px auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
    th { background: #f5f5f5; }
    pre { white-space: pre-wrap; }
  </style>
</head>
<body>
  <h1>Результаты опроса</h1>
  <p><a href="/index.html">← На страницу опроса</a></p>

  <?php if (!$rows): ?>
    <p>Пока нет ни одной попытки.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Пользователь</th>
          <th>Балл</th>
          <th>Дата</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['score']) ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
