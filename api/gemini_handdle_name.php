<?php
error_reporting(0);
ini_set('display_errors', '0');
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

/* Params — 프론트에서 전달받는 값 */
$channelName = trim($body["channelName"] ?? "");
$userInput   = trim($body["userInput"]   ?? "");

/* Gemini — key를 URL 쿼리 파라미터로 전달 */
$model = "gemini-2.0-flash";
$url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

/* Prompt — 유튜브 핸들 규칙 반영 */
$prompt = <<<PROMPT
You are a YouTube handle naming expert.
Generate 5 unique YouTube handle name suggestions.

YouTube Channel Name: {$channelName}
User preferred handle (optional): {$userInput}

YouTube Handle Rules (MUST follow ALL):
- Must contain ONLY lowercase English letters (a-z), digits (0-9), underscores (_), or periods (.)
- NO spaces, NO hyphens, NO special characters, NO uppercase letters
- Length must be between 3 and 30 characters (excluding the @ prefix)
- Do NOT include the @ prefix in the output
- Must be easy to type and remember
- Should relate to the channel name

If user provided a preferred handle, create variations based on it.
If no preferred handle, derive handles from the channel name.

Return ONLY valid JSON. No markdown, no explanation.
Schema: {"names":["handle1","handle2","handle3","handle4","handle5"]}
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

/* Debug log */
file_put_contents(
    __DIR__ . '/gemini_handle_debug.log',
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

/* Normalize — 핸들 규칙에 맞게 후처리 */
$names = [];
foreach ($decoded["names"] as $raw_name) {
    $h = trim($raw_name);
    // @ 접두사 제거
    $h = ltrim($h, '@');
    // 소문자 변환
    $h = strtolower($h);
    // 허용 문자만 남기기 (영문소문자, 숫자, 밑줄, 마침표)
    $h = preg_replace('/[^a-z0-9_.]/', '', $h);
    // 길이 제한 (3~30)
    if (strlen($h) >= 3 && strlen($h) <= 30) {
        $names[] = $h;
    }
}

$names = array_slice(array_unique($names), 0, 5);

if (empty($names)) {
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => "No valid handles generated",
        "raw"   => $decoded["names"]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Success */
echo json_encode(["ok"=>true,"names"=>$names], JSON_UNESCAPED_UNICODE);
exit;
