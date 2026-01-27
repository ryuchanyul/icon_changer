<?php
error_reporting(0);
ini_set('display_errors', '0');
ob_start();
header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode([
            "ok"    => false,
            "error" => "PHP Fatal: " . $err['message'],
            "file"  => basename($err['file']),
            "line"  => $err['line']
        ], JSON_UNESCAPED_UNICODE);
    }
});

try {

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok"=>false,"error"=>"POST only"], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"Invalid JSON"], JSON_UNESCAPED_UNICODE);
    exit;
}

$apiKey = trim($body["apiKey"] ?? "");
if ($apiKey === "") {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"Missing apiKey"], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Params */
$type        = trim($body["type"] ?? "");        // "icon" or "art"
$channelName = trim($body["channelName"] ?? "");
$handleName  = trim($body["handleName"] ?? "");
$description = trim($body["description"] ?? "");
$keywords    = trim($body["keywords"] ?? "");

if (!in_array($type, ["icon", "art"])) {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"type must be 'icon' or 'art'"], JSON_UNESCAPED_UNICODE);
    exit;
}

/* 프롬프트 생성 */
if ($type === "icon") {
    $prompt  = "Create a professional YouTube channel profile icon/logo. ";
    $prompt .= "Channel name: " . $channelName . ". ";
    $prompt .= "Channel description: " . $description . ". ";
    $prompt .= "Style: Modern, clean, minimalist logo design. ";
    $prompt .= "The icon should be simple, recognizable, and work well at small sizes. ";
    $prompt .= "Use bold colors and clean shapes. No text in the image. ";
    $prompt .= "Square format, suitable for a YouTube profile picture.";
} else {
    $prompt  = "Create a professional YouTube channel banner/art. ";
    $prompt .= "Channel name: " . $channelName . ". ";
    $prompt .= "Channel description: " . $description . ". ";
    $prompt .= "Style: Modern, cinematic, visually striking banner. ";
    $prompt .= "Wide landscape format (16:9 ratio). ";
    $prompt .= "Professional quality, suitable for a YouTube channel header. ";
    $prompt .= "Include subtle visual elements related to the channel theme. No text.";
}

/* Gemini 2.5 Flash Image API */
$model = "gemini-2.5-flash-image";
$url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . urlencode($apiKey);

$aspectRatio = ($type === "icon") ? "1:1" : "16:9";

$postData = [
    "contents" => [[
        "role" => "user",
        "parts" => [["text" => $prompt]]
    ]],
    "generationConfig" => [
        "responseModalities" => ["IMAGE"],
        "imageConfig" => [
            "aspectRatio" => $aspectRatio
        ]
    ]
];

@file_put_contents(
    __DIR__ . '/gemini_image_debug.log',
    date('Y-m-d H:i:s') . " [REQUEST] type=" . $type . "\nPrompt: " . $prompt . "\n\n",
    FILE_APPEND
);

/* cURL */
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS     => json_encode($postData, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT        => 60,
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
        "error" => "Gemini Image HTTP " . $httpCode,
        "raw"   => json_decode($response, true) ?? $response
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$respJson = json_decode($response, true);

@file_put_contents(
    __DIR__ . '/gemini_image_debug.log',
    date('Y-m-d H:i:s') . " [RESPONSE] keys=" . implode(',', array_keys($respJson ?? [])) . "\n\n",
    FILE_APPEND
);

/* 이미지 추출 — 응답 형식 호환 */
$imageBase64 = "";
$mimeType = "image/png";

// Imagen 3 형식: generatedImages[0].image.imageBytes
if (isset($respJson["generatedImages"][0]["image"]["imageBytes"])) {
    $imageBase64 = $respJson["generatedImages"][0]["image"]["imageBytes"];
    $mimeType = $respJson["generatedImages"][0]["image"]["mimeType"] ?? "image/png";
}
// Gemini 형식: candidates[0].content.parts[0].inlineData
elseif (isset($respJson["candidates"][0]["content"]["parts"])) {
    foreach ($respJson["candidates"][0]["content"]["parts"] as $part) {
        if (isset($part["inlineData"]["data"])) {
            $imageBase64 = $part["inlineData"]["data"];
            $mimeType = $part["inlineData"]["mimeType"] ?? "image/png";
            break;
        }
    }
}
// predictions 형식
elseif (isset($respJson["predictions"][0]["bytesBase64Encoded"])) {
    $imageBase64 = $respJson["predictions"][0]["bytesBase64Encoded"];
}

if (!$imageBase64) {
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => "No image in response",
        "raw"   => $respJson
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Success */
ob_end_clean();
echo json_encode([
    "ok"       => true,
    "type"     => $type,
    "mimeType" => $mimeType,
    "image"    => $imageBase64
], JSON_UNESCAPED_UNICODE);
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
