<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'POST 요청만 허용됩니다.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['ok' => false, 'error' => '잘못된 요청 데이터입니다.']);
    exit;
}

$apiKey = $input['apiKey'] ?? '';
$type = $input['type'] ?? 'intro';
$channelName = $input['channelName'] ?? '';
$handleName = $input['handleName'] ?? '';
$description = $input['description'] ?? '';
$keywords = $input['keywords'] ?? '';

if (!$apiKey) {
    echo json_encode(['ok' => false, 'error' => 'API 키가 필요합니다.']);
    exit;
}

// 인트로 이미지 프롬프트 생성
$prompt = "Create a visually stunning YouTube channel intro image for a video opening sequence.\n";
$prompt .= "Channel name: \"{$channelName}\"\n";

if ($handleName) {
    $prompt .= "Handle: @{$handleName}\n";
}
if ($description) {
    $prompt .= "Channel theme: {$description}\n";
}
if ($keywords) {
    $prompt .= "Keywords: {$keywords}\n";
}

$prompt .= "\nDesign requirements:\n";
$prompt .= "- Modern, cinematic intro image suitable for a YouTube video opening\n";
$prompt .= "- Display the channel name \"{$channelName}\" prominently in the center\n";
$prompt .= "- Use vibrant, eye-catching colors with gradient or dynamic background\n";
$prompt .= "- Professional and polished design\n";
$prompt .= "- Square format (1:1 aspect ratio)\n";
$prompt .= "- Clean typography, no clutter\n";
$prompt .= "- Suitable for video intro/outro overlay\n";

// Gemini API 호출
$model = 'gemini-2.0-flash-exp';
$url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

$requestBody = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'responseModalities' => ['TEXT', 'IMAGE']
    ]
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($requestBody),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo json_encode(['ok' => false, 'error' => 'API 연결 실패: ' . $curlError]);
    exit;
}

if ($httpCode !== 200) {
    $errorData = json_decode($response, true);
    $errorMessage = $errorData['error']['message'] ?? "HTTP {$httpCode} 오류";
    echo json_encode(['ok' => false, 'error' => 'Gemini API 오류: ' . $errorMessage]);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode(['ok' => false, 'error' => 'API 응답 파싱 실패']);
    exit;
}

// 응답에서 이미지 데이터 추출
$imageData = null;
$mimeType = 'image/png';

if (isset($data['candidates'][0]['content']['parts'])) {
    foreach ($data['candidates'][0]['content']['parts'] as $part) {
        if (isset($part['inlineData'])) {
            $imageData = $part['inlineData']['data'];
            $mimeType = $part['inlineData']['mimeType'] ?? 'image/png';
            break;
        }
    }
}

if (!$imageData) {
    // 이미지가 없는 경우 텍스트 응답 확인
    $textResponse = '';
    if (isset($data['candidates'][0]['content']['parts'])) {
        foreach ($data['candidates'][0]['content']['parts'] as $part) {
            if (isset($part['text'])) {
                $textResponse .= $part['text'];
            }
        }
    }
    echo json_encode([
        'ok' => false,
        'error' => '이미지 생성에 실패했습니다.' . ($textResponse ? ' 응답: ' . mb_substr($textResponse, 0, 200) : '')
    ]);
    exit;
}

echo json_encode([
    'ok' => true,
    'image' => $imageData,
    'mimeType' => $mimeType
]);
