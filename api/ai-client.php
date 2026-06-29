<?php
declare(strict_types=1);

/*
 * Anthropic (Claude) Messages API client.
 *
 * AÇARI QURMAQ: api/config.local.php faylında əlavə et:
 *   define('DL_AI_KEY', 'sk-ant-...');           // Anthropic API açarı (məcburi)
 *   define('DL_AI_MODEL', 'claude-haiku-4-5-20251001'); // istəyə bağlı
 *
 * Açar olmadıqda AI funksiyaları sadəcə "konfiqurasiya olunmayıb" mesajı verir.
 */

require_once __DIR__ . '/config.php';

defined('DL_AI_KEY')   || define('DL_AI_KEY', getenv('DL_AI_KEY') ?: '');
defined('DL_AI_MODEL') || define('DL_AI_MODEL', getenv('DL_AI_MODEL') ?: 'claude-haiku-4-5-20251001');
defined('DL_AI_URL')   || define('DL_AI_URL', getenv('DL_AI_URL') ?: 'https://api.anthropic.com/v1/messages');
defined('DL_AI_VERSION') || define('DL_AI_VERSION', getenv('DL_AI_VERSION') ?: '2023-06-01');

function dl_ai_available(): bool
{
    return DL_AI_KEY !== '';
}

/**
 * @param array $messages [['role'=>'user'|'assistant','content'=>string], ...]
 * @return string assistant cavabı (mətn)
 * @throws RuntimeException
 */
function dl_ai_chat(string $system, array $messages, int $maxTokens = 1024): string
{
    if (DL_AI_KEY === '') {
        throw new RuntimeException('AI açarı konfiqurasiya olunmayıb. api/config.local.php-də DL_AI_KEY əlavə et.');
    }

    $payload = json_encode([
        'model' => DL_AI_MODEL,
        'max_tokens' => $maxTokens,
        'system' => $system,
        'messages' => $messages,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $ch = curl_init(DL_AI_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'content-type: application/json',
            'x-api-key: ' . DL_AI_KEY,
            'anthropic-version: ' . DL_AI_VERSION,
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_CONNECTTIMEOUT => 15,
    ]);
    $resp = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        throw new RuntimeException('AI bağlantı xətası: ' . $curlErr);
    }

    $data = json_decode($resp, true);
    if (!is_array($data)) {
        throw new RuntimeException('AI cavabı oxunmadı.');
    }
    if ($httpCode >= 400) {
        $msg = $data['error']['message'] ?? ('HTTP ' . $httpCode);
        throw new RuntimeException('AI xətası: ' . $msg);
    }

    $text = '';
    foreach (($data['content'] ?? []) as $block) {
        if (($block['type'] ?? '') === 'text') {
            $text .= (string) ($block['text'] ?? '');
        }
    }
    $text = trim($text);
    if ($text === '') {
        throw new RuntimeException('AI boş cavab qaytardı.');
    }
    return $text;
}
