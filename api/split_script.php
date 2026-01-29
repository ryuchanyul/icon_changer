<?php
/**
 * 대본 분할 API (Gemini)
 * 스토리를 문맥에 맞춰 이미지 생성에 적합한 씬(문단)으로 분리
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'POST 요청만 허용됩니다.']);
    exit;
}

// 입력 데이터 받기
$input = json_decode(file_get_contents('php://input'), true);

$script = $input['script'] ?? '';
$style = $input['style'] ?? '';
$ratio = $input['ratio'] ?? '16:9';
$apiKey = $input['apiKey'] ?? '';  // Gemini API Key

if (empty($script)) {
    echo json_encode(['ok' => false, 'error' => '대본이 입력되지 않았습니다.']);
    exit;
}

if (empty($apiKey)) {
    echo json_encode(['ok' => false, 'error' => 'Gemini API 키가 필요합니다.']);
    exit;
}

// 대본 길이 체크 (약 20분 = 4000~6000자 기준)
$scriptLength = mb_strlen($script, 'UTF-8');

// 예상 씬 개수 계산 (약 200~300자당 1씬)
$estimatedScenes = max(5, min(30, ceil($scriptLength / 250)));

// Gemini API 호출을 위한 프롬프트
$prompt = <<<PROMPT
당신은 영상 제작을 위한 대본 분석 전문가입니다.
주어진 대본을 이미지 생성에 적합한 씬(장면)으로 분할해야 합니다.

## 규칙:
1. 각 씬은 하나의 명확한 시각적 장면을 나타내야 합니다.
2. 씬 전환은 장소 변경, 시간 경과, 주요 행동 변화 시점에서 이루어집니다.
3. 각 씬에 대해 이미지 생성용 프롬프트(영문)를 작성합니다.
4. 프롬프트는 구체적인 시각 요소(배경, 인물, 행동, 분위기)를 포함해야 합니다.
5. 약 {$estimatedScenes}개 내외의 씬으로 분할하세요.

## 출력 형식 (반드시 JSON만 출력):
{
  "scenes": [
    {
      "sceneNumber": 1,
      "koreanText": "원본 대본의 해당 부분 (한국어)",
      "summary": "씬 요약 (한국어, 1줄)",
      "imagePrompt": "Detailed image generation prompt in English, including style: {$style}",
      "duration": "예상 길이 (초)",
      "mood": "분위기 키워드"
    }
  ],
  "totalScenes": 씬총개수,
  "estimatedDuration": "전체 예상 시간"
}

## 영상 설정:
- 화면 비율: {$ratio}
- 스타일: {$style}

## 대본:
{$script}

위 대본을 분석하여 JSON 형식으로만 응답해주세요. 다른 설명 없이 JSON만 출력하세요.
PROMPT;

// Gemini API 호출
$response = callGemini($apiKey, $prompt);

if ($response['ok']) {
    echo json_encode([
        'ok' => true,
        'data' => $response['data'],
        'scriptLength' => $scriptLength,
        'estimatedScenes' => $estimatedScenes
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'ok' => false,
        'error' => $response['error']
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Gemini API 호출 함수
 */
function callGemini($apiKey, $prompt) {
    // Gemini 1.5 Flash (빠르고 저렴) 또는 Gemini 1.5 Pro 사용
    $model = 'gemini-1.5-flash';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 8192,
            'responseMimeType' => 'application/json'
        ],
        'safetySettings' => [
            ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
            ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
        ]
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 120
    ]);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['ok' => false, 'error' => 'API 연결 오류: ' . $error];
    }

    if ($httpCode !== 200) {
        $errorData = json_decode($result, true);
        $errorMessage = $errorData['error']['message'] ?? 'Gemini API 오류 (HTTP ' . $httpCode . ')';
        return ['ok' => false, 'error' => $errorMessage];
    }

    $responseData = json_decode($result, true);

    // Gemini 응답 구조에서 텍스트 추출
    $content = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($content)) {
        // 차단된 경우 확인
        $blockReason = $responseData['candidates'][0]['finishReason'] ?? '';
        if ($blockReason === 'SAFETY') {
            return ['ok' => false, 'error' => '안전 필터에 의해 차단되었습니다.'];
        }
        return ['ok' => false, 'error' => 'AI 응답이 비어있습니다.'];
    }

    // JSON 파싱 (마크다운 코드 블록 제거)
    $content = trim($content);
    $content = preg_replace('/^```json\s*/i', '', $content);
    $content = preg_replace('/^```\s*/i', '', $content);
    $content = preg_replace('/\s*```$/i', '', $content);

    $parsedContent = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['ok' => false, 'error' => 'AI 응답 파싱 오류: ' . json_last_error_msg()];
    }

    return ['ok' => true, 'data' => $parsedContent];
}
