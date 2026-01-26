<?php
header('Content-Type: application/json; charset=utf-8');

/* POST only */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok"=>false,"error"=>"POST only"], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Input JSON */
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"Invalid JSON","raw"=>$raw], JSON_UNESCAPED_UNICODE);
    exit;
}

/* API Key */
$apiKey = trim($body["apiKey"] ?? "");
if ($apiKey === "") {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"Missing apiKey"], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Params */
$country = $body["country"]  ?? "";
$lang    = $body["language"] ?? "";
$ytCat   = $body["category"] ?? "";
$topic   = $body["topic"]    ?? "";
$age     = $body["age"]      ?? "";
$style   = $body["style"]    ?? "";
$format  = $body["format"]   ?? "";
$brand   = $body["brand"]    ?? "";

/* Gemini — key를 URL 쿼리 파라미터로 전달 */
$model = "gemini-2.0-flash";
$url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

/* Prompt */
$prompt = <<<PROMPT
You are a YouTube channel naming expert.
Based on the following parameters, suggest 5 unique, creative, and memorable YouTube channel names.

Country: {$country}
Language: {$lang}
YouTube Category: {$ytCat}
Topic: {$topic}
Target Age Group: {$age}
Style: {$style}
Content Format: {$format}
Brand Tendency: {$brand}

Rules:
- Names must be in {$lang}.
- Each name should be concise (1-4 words).
- Names should be catchy and easy to remember.
- Names should reflect the topic and style.

Return ONLY valid JSON. No markdown, no explanation.
Schema: {"names":["name1","name2","name3","name4","name5"]}
PROMPT;

/* Payload */
$postData = [
  "contents" => [[
    "role" => "user",
    "parts" => [["text" => $prompt]]
  ]],
  "generationConfig" => [
    "temperature" => 0.9,
    "maxOutputTokens" => 512,
    "responseMimeType" => "application/json"
  ]
];

/* cURL */
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS     => json_encode($postData, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>"cURL error","detail"=>$err], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($httpCode < 200 || $httpCode >= 300) {
    http_response_code(502);
    echo json_encode([
        "ok"    => false,
        "error" => "Gemini HTTP {$httpCode}",
        "raw"   => json_decode($response, true) ?? $response
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Parse Gemini */
$respJson = json_decode($response, true);

/* Debug log (optional) */
$logDir = __DIR__;
file_put_contents(
    $logDir . '/gemini_debug.log',
    date('Y-m-d H:i:s') . "\n" . json_encode($respJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n",
    FILE_APPEND
);

$text = $respJson["candidates"][0]["content"]["parts"][0]["text"] ?? "";

if (!$text) {
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>"Empty Gemini response","raw"=>$respJson], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Cleanup — markdown 코드블록 제거 */
$text = trim(preg_replace('/```json|```/i', '', $text));

/* Decode */
$decoded = json_decode($text, true);

if (!is_array($decoded) || !isset($decoded["names"]) || !is_array($decoded["names"])) {
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => "Failed to parse names JSON",
        "text"  => $text
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Normalize — 빈 값 제거 후 최대 5개 */
$names = array_slice(
    array_values(array_filter(array_map('trim', $decoded["names"]), function($v) {
        return $v !== '';
    })),
    0,
    5
);

/* Success */
echo json_encode(["ok"=>true,"names"=>$names], JSON_UNESCAPED_UNICODE);
exit;
