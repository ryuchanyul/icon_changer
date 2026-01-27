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
$prompt .= "Translation Rules:\n";
$prompt .= "- Also provide a Korean translation for each description.\n\n";
$prompt .= "Return ONLY valid JSON. No markdown, no explanation.\n";
$prompt .= 'Schema: {"names":["desc1","desc2"],"translations":["한국어설명1","한국어설명2"],"keywords":["#tag1","#tag2","#tag3","#tag4","#tag5","#tag6","#tag7","#tag8","#tag9","#tag10"]}';

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

/* Gemini 응답이 배열이면 첫 번째 요소 사용 */
if (is_array($decoded) && isset($decoded[0]) && is_array($decoded[0])) {
    $decoded = $decoded[0];
}

/* 유연한 파싱 — Gemini가 다양한 키를 사용할 수 있음 */
$names = [];
$translations = [];
$keywords = [];

if (is_array($decoded)) {

    /* 1) descriptions/names 배열이 있는 경우 */
    if (isset($decoded["names"]) && is_array($decoded["names"])) {
        $names = $decoded["names"];
    } elseif (isset($decoded["descriptions"]) && is_array($decoded["descriptions"])) {
        $names = $decoded["descriptions"];
    } else {
        /* 2) desc1, desc2... 또는 번호 키로 된 경우 */
        foreach ($decoded as $key => $val) {
            if (is_string($val) && preg_match('/^desc/i', $key)) {
                $names[] = $val;
            }
        }
    }

    /* translations 배열이 있는 경우 */
    if (isset($decoded["translations"]) && is_array($decoded["translations"])) {
        $translations = $decoded["translations"];
    } else {
        /* 한국어설명1, 한국어설명2... 키로 된 경우 */
        foreach ($decoded as $key => $val) {
            if (is_string($val) && preg_match('/^한국어/', $key)) {
                $translations[] = $val;
            }
        }
    }

    /* keywords */
    if (isset($decoded["keywords"]) && is_array($decoded["keywords"])) {
        $keywords = $decoded["keywords"];
    }
}

if (empty($names)) {
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => "No descriptions found in response",
        "text"  => $text
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Normalize — 문자열이 아닌 요소 방어 */
$cleanNames = [];
foreach ($names as $v) {
    if (is_array($v)) $v = implode(' ', array_map('strval', $v));
    if (!is_string($v)) $v = strval($v);
    $v = trim($v);
    if ($v !== '') $cleanNames[] = $v;
}
$names = array_slice($cleanNames, 0, 2);

if (empty($names)) {
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => "No valid descriptions generated",
        "raw"   => $decoded
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Translations normalize */
$cleanTranslations = [];
foreach ($translations as $t) {
    if (is_array($t)) $t = implode(' ', array_map('strval', $t));
    if (!is_string($t)) $t = strval($t);
    $cleanTranslations[] = trim($t);
}
$translations = array_slice($cleanTranslations, 0, count($names));

/* Keywords normalize */
$cleanKeywords = [];
foreach ($keywords as $kw) {
    if (is_array($kw)) $kw = implode(' ', array_map('strval', $kw));
    if (!is_string($kw)) $kw = strval($kw);
    $kw = trim($kw);
    if ($kw !== '') $cleanKeywords[] = $kw;
}
$keywords = array_slice($cleanKeywords, 0, 10);

/* Success */
ob_end_clean();
echo json_encode(["ok"=>true,"names"=>$names,"translations"=>$translations,"keywords"=>$keywords], JSON_UNESCAPED_UNICODE);
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
