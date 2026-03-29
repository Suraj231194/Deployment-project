<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables manually if not using Laravel's bootstrap
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[$name] = trim($value);
        putenv("$name=$value");
    }
}

$apiKey = getenv('ANTHROPIC_API_KEY');

if (!$apiKey) {
    die("Error: ANTHROPIC_API_KEY not found in .env\n");
}

echo "Testing Anthropic API with key: " . substr($apiKey, 0, 10) . "..." . substr($apiKey, -4) . " (Length: " . strlen($apiKey) . ")\n";

$data = [
    'model' => 'claude-3-5-sonnet-20241022',
    'max_tokens' => 1024,
    'messages' => [
        ['role' => 'user', 'content' => 'Hello, Claude! Please respond with "Verification successful." if you can hear me.']
    ]
];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local testing only
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);    // For local testing only
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $apiKey,
    'anthropic-version: 2023-06-01',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch) . "\n";
} else {
    echo "HTTP Status Code: $httpCode\n";
    $result = json_decode($response, true);
    if (isset($result['content'][0]['text'])) {
        echo "Claude's response: " . $result['content'][0]['text'] . "\n";
    } else {
        echo "Error response: " . print_r($result, true) . "\n";
    }
}

curl_close($ch);
