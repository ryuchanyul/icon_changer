<?php
error_reporting(0);
ini_set('display_errors', '0');
ob_start();
header('Content-Type: application/json; charset=utf-8');

/* Fatal Error 캐치 — 스크립트 비정상 종료 시 JSON 반환 */
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode([
            "ok"    => false,
            "error" => "PHP Fatal: {$err['message']}",
            "file"  => basename($err['file']),
            "line"  => $err['line']
        ], JSON_UNESCAPED_UNICODE);
    }
});

try {

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
$channelName = trim($body["channelName"] ?? "");
$handleName  = trim($body["handleName"]  ?? "");
$userInput   = trim($body["userInput"]   ?? "");

/* Gemini */
$model = "gemini-2.0-flash";
$url   = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . urlencode($apiKey);

/* Prompt */
$prompt  = "You are a YouTube channel description and keyword expert.\n";
$prompt .= "Generate 2 unique channel descriptions AND 10 relevant hashtag keywords.\n\n";
$prompt .= "YouTube Channel Name: " . $channelName . "\n";
$prompt .= "YouTube Handle: @" . $handleName . "\n";
$prompt .= "User reference notes: " . $userInput . "\n\n";
$prompt .= "Description Rules:\n";
$prompt .= "- Each description must be 2-4 sentences.\n";
$prompt .= "- Must be under 1000 characters.\n";
$prompt .= "- Should clearly convey the channel's purpose and value to viewers.\n";
$prompt .= "- Should include relevant keywords for YouTube SEO.\n";
$prompt .= "- Tone should match the channel name's style.\n";
$prompt .= "- Write in the same language as the channel name.\n\n";
$prompt .= "Keyword Rules:\n";
$prompt .= "- Generate 10 hashtag keywords relevant to the channel.\n";
$prompt .= "- Each keyword must start with # symbol.\n";
$prompt .= "- Keywords should be in the same language as the channel name.\n";
$prompt .= "- Mix broad and niche keywords for SEO.\n\n";
$prompt .= "Return ONLY valid JSON. No markdown, no explanation.\n";
$prompt .= 'Schema: {"names":["desc1","desc2"],"keywords":["#tag1","#tag2","#tag3","#tag4","#tag5","#tag6","#tag7","#tag8","#tag9","#tag10"]}';

/* Payload */
$postData = [
  "contents" => [[
    "role" => "user",
    "parts" => [["text" => $prompt]]
  ]],
  "generationConfig" => [
    "temperature" => 0.9,
    "maxOutputTokens" => 2048,
    "responseMimeType" => "application/json"
  ]
];

/* cURL */
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
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
        "error" => "Gemini HTTP " . $httpCode,
        "raw"   => json_decode($response, true) ?? $response
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Parse Gemini */
$respJson = json_decode($response, true);

/* Debug log */
@file_put_contents(
    __DIR__ . '/gemini_description_debug.log',
    date('Y-m-d H:i:s') . "\n" . json_encode($respJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n",
    FILE_APPEND
);

$text = "";
if (isset($respJson["candidates"][0]["content"]["parts"][0]["text"])) {
    $text = $respJson["candidates"][0]["content"]["parts"][0]["text"];
}

if (!$text) {
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>"Empty Gemini response","raw"=>$respJson], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Cleanup */
$text = trim(preg_replace('/```json|```/i', '', $text));

/* Decode */
$decoded = json_decode($text, true);

/* Gemini가 "names" 대신 "descriptions" 키를 쓸 수 있으므로 둘 다 허용 */
if (is_array($decoded)) {
    if (!isset($decoded["names"]) && isset($decoded["descriptions"])) {
        $decoded["names"] = $decoded["descriptions"];
    }
}

if (!is_array($decoded) || !isset($decoded["names"]) || !is_array($decoded["names"])) {
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => "Failed to parse description JSON",
        "text"  => $text
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Normalize — 문자열이 아닌 요소 방어 */
$names = [];
foreach ($decoded["names"] as $v) {
    if (is_array($v)) {
        $v = implode(' ', array_map('strval', $v));
    }
    if (!is_string($v)) {
        $v = strval($v);
    }
    $v = trim($v);
    if ($v !== '') $names[] = $v;
}
$names = array_slice($names, 0, 2);

if (empty($names)) {
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => "No valid descriptions generated",
        "raw"   => $decoded["names"]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Keywords 처리 */
$keywords = [];
if (isset($decoded["keywords"]) && is_array($decoded["keywords"])) {
    foreach ($decoded["keywords"] as $kw) {
        if (is_array($kw)) {
            $kw = implode(' ', array_map('strval', $kw));
        }
        if (!is_string($kw)) {
            $kw = strval($kw);
        }
        $kw = trim($kw);
        if ($kw !== '') $keywords[] = $kw;
    }
}
$keywords = array_slice($keywords, 0, 10);

/* Success */
ob_end_clean();
echo json_encode(["ok"=>true,"names"=>$names,"keywords"=>$keywords], JSON_UNESCAPED_UNICODE);
exit;

} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => "Exception: " . $e->getMessage(),
        "file"  => basename($e->getFile()),
        "line"  => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
