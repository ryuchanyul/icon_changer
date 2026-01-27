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

/* Params */
$apiKey       = trim($input['apiKey'] ?? '');
$type         = trim($input['type'] ?? 'intro');
$channelName  = trim($input['channelName'] ?? '');
$handleName   = trim($input['handleName'] ?? '');
$description  = trim($input['description'] ?? '');
$keywords     = trim($input['keywords'] ?? '');
$country      = trim($input['country'] ?? '');
$imageStyle   = trim($input['imageStyle'] ?? '');

if (!$apiKey) {
    echo json_encode(['ok' => false, 'error' => 'API 키가 필요합니다.']);
    exit;
}

// 국가명 한국어 → 영어 매핑
$countryMap = [
    '미국'   => 'United States',
    '한국'   => 'South Korea',
    '일본'   => 'Japan',
    '페루'   => 'Peru',
    '멕시코' => 'Mexico',
    '칠레'   => 'Chile',
];
$countryEN = $countryMap[$country] ?? $country;

// 인트로 이미지 프롬프트 생성 (스타일별 분기)
$prompt = '';

if ($imageStyle === '효과') {
    // ── 효과: 추상 비주얼 인트로 ──
    $prompt .= "Generate a cinematic YouTube intro image.\n";
    $prompt .= "Style: abstract visual effects, glowing light trails, dynamic motion graphics, particle explosion, neon gradients.\n";
    $prompt .= "Mood: energetic, futuristic, high-impact opening sequence.\n";
    $prompt .= "Square format (1:1 aspect ratio), high resolution, ultra detailed.\n";
    $prompt .= "Negative: no text, no human, no face, no letters, no watermark.\n";

} elseif ($imageStyle === '관광명소') {
    // ── 관광명소: 국가별 랜덤 랜드마크 ──
    $prompt .= "Generate a stunning cinematic photo of a random famous tourist landmark in {$countryEN}.\n";
    $prompt .= "Style: epic wide-angle landscape photography, golden hour lighting, vivid colors, travel documentary look.\n";
    $prompt .= "Show the landmark as the hero subject with dramatic sky and atmosphere.\n";
    $prompt .= "Square format (1:1 aspect ratio), high resolution, ultra detailed.\n";
    $prompt .= "Negative: no text, no human, no face, no letters, no watermark, no person.\n";

} elseif ($imageStyle === '도시') {
    // ── 도시: 국가별 랜덤 도시 풍경 ──
    $prompt .= "Generate a stunning cinematic photo of a random famous city skyline or cityscape in {$countryEN}.\n";
    $prompt .= "Style: modern urban photography, dramatic lighting, aerial or street-level view, vibrant city lights.\n";
    $prompt .= "Show iconic buildings, streets, or skyline with atmospheric mood.\n";
    $prompt .= "Square format (1:1 aspect ratio), high resolution, ultra detailed.\n";
    $prompt .= "Negative: no text, no human, no face, no letters, no watermark, no person.\n";

} else {
    // ── 기본 폴백 ──
    $prompt .= "Generate a cinematic YouTube intro image.\n";
    $prompt .= "Style: modern, eye-catching, professional.\n";
    $prompt .= "Square format (1:1 aspect ratio), high resolution.\n";
    $prompt .= "Negative: no text, no human, no face, no letters, no watermark.\n";
}

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
