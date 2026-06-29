<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

function respond_public(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function public_setting(PDO $pdo, string $name): array
{
    $stmt = $pdo->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
    $stmt->execute([$name]);
    $value = $stmt->fetchColumn();

    if (!is_string($value) || $value === '') {
        return [];
    }

    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : [];
}

try {
    $action = $_GET['action'] ?? 'site';
    if ($action !== 'site') {
        respond_public(['ok' => false, 'message' => 'Unknown action.'], 404);
    }

    $pdo = db();
    respond_public([
        'ok' => true,
        'data' => [
            'content' => public_setting($pdo, 'content'),
            'seo' => public_setting($pdo, 'seo'),
        ],
    ]);
} catch (Throwable $error) {
    respond_public(['ok' => false, 'message' => 'Content could not be loaded.'], 500);
}
