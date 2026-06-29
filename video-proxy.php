<?php
declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
    header('Access-Control-Allow-Headers: Range, Content-Type');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

$fileId = $_GET['id'] ?? '';

if (!preg_match('/^[A-Za-z0-9_-]{10,}$/', $fileId)) {
    proxy_error(400, 'Invalid video id.');
}

if (!function_exists('curl_init')) {
    proxy_error(500, 'PHP cURL extension is not enabled on this server.');
}

$range = $_SERVER['HTTP_RANGE'] ?? '';
$cookieFile = tempnam(sys_get_temp_dir(), 'datalab_drive_');
if ($cookieFile === false) {
    proxy_error(500, 'Could not create temporary cookie file.');
}

register_shutdown_function(static function () use ($cookieFile): void {
    if (is_file($cookieFile)) {
        @unlink($cookieFile);
    }
});

set_time_limit(0);
header_remove('X-Powered-By');
header('Access-Control-Allow-Origin: *');
header('Accept-Ranges: bytes');

$source = resolve_drive_source($fileId, $cookieFile);
stream_drive_video($source, $range, $cookieFile);

function proxy_error(int $status, string $message): void
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');
    }
    echo $message;
    exit;
}

function drive_headers(string $range = ''): array
{
    $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125 Safari/537.36',
        'Accept: */*',
        'Referer: https://drive.google.com/',
        'Connection: keep-alive',
    ];

    if ($range !== '') {
        $headers[] = 'Range: ' . $range;
    }

    return $headers;
}

function curl_base(string $url, string $cookieFile)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => '',
    ]);
    return $ch;
}

function resolve_drive_source(string $fileId, string $cookieFile): string
{
    $initial = 'https://drive.google.com/uc?export=download&id=' . rawurlencode($fileId);
    $ch = curl_base($initial, $cookieFile);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => false,
        CURLOPT_HTTPHEADER => drive_headers('bytes=0-0'),
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        proxy_error(502, 'Google Drive request failed: ' . $error);
    }

    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $contentType = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $effectiveUrl = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $headers = substr((string) $response, 0, $headerSize);
    $body = substr((string) $response, $headerSize);
    curl_close($ch);

    if ($status < 400 && $contentType !== '' && stripos($contentType, 'text/html') === false) {
        return $effectiveUrl !== '' ? $effectiveUrl : $initial;
    }

    $token = '';
    if (preg_match('/confirm=([0-9A-Za-z_-]+)/', $body, $match)) {
        $token = $match[1];
    } elseif (preg_match('/download_warning[^=]*=([^;\s]+)/i', $headers, $match)) {
        $token = trim($match[1]);
    }

    if ($token !== '') {
        return 'https://drive.google.com/uc?export=download&confirm=' . rawurlencode($token) . '&id=' . rawurlencode($fileId);
    }

    return 'https://drive.usercontent.google.com/download?id=' . rawurlencode($fileId) . '&export=download&confirm=t';
}

function stream_drive_video(string $source, string $range, string $cookieFile): void
{
    $allowedHeaders = [
        'content-type',
        'content-length',
        'content-range',
        'accept-ranges',
        'last-modified',
        'etag',
    ];

    $sentBodyHeader = false;
    $ch = curl_base($source, $cookieFile);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => drive_headers($range),
        CURLOPT_HEADERFUNCTION => static function ($curl, string $header) use ($allowedHeaders, &$sentBodyHeader): int {
            $length = strlen($header);
            $header = trim($header);

            if ($header === '') {
                return $length;
            }

            if (preg_match('/^HTTP\/\S+\s+(\d+)/', $header, $match)) {
                $code = (int) $match[1];
                if ($code === 200 || $code === 206) {
                    http_response_code($code);
                    $sentBodyHeader = true;
                } elseif ($code >= 400) {
                    http_response_code($code);
                }
                return $length;
            }

            if (!$sentBodyHeader) {
                return $length;
            }

            $name = strtolower(strtok($header, ':') ?: '');
            if (in_array($name, $allowedHeaders, true) && !headers_sent()) {
                header($header, false);
            }

            return $length;
        },
        CURLOPT_WRITEFUNCTION => static function ($curl, string $chunk): int {
            echo $chunk;
            flush();
            return strlen($chunk);
        },
    ]);

    $ok = curl_exec($ch);
    if ($ok === false && !headers_sent()) {
        proxy_error(502, 'Video stream could not be loaded: ' . curl_error($ch));
    }

    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($status >= 400 && !headers_sent()) {
        proxy_error(502, 'Google Drive returned HTTP ' . $status . '. Make sure the file is shared with link access.');
    }
}