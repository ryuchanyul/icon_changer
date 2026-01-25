<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API 설정 - Bbanana.ai</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* 헤더 - 전체 너비 */
        .header {
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header-left {
            flex-shrink: 0;
        }

        .header h1 {
            color: #6f2dff;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
            cursor: pointer;
        }

        .header-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .menu-nav {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: nowrap;
        }

        .menu-nav a {
            color: #666;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.5rem 0.6rem;
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
            white-space: nowrap;
        }

        .menu-nav a:hover {
            color: #6f2dff;
            background: rgba(111, 45, 255, 0.1);
        }

        .menu-nav a.active {
            color: #6f2dff;
            background: rgba(111, 45, 255, 0.15);
        }

        .header-right {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-shrink: 0;
        }

        .login-btn {
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #6f2dff 0%, #5a1fd9 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(111, 45, 255, 0.3);
            white-space: nowrap;
            font-size: 0.85rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(111, 45, 255, 0.4);
        }

        /* 메인 콘텐츠 영역 */
        .content-area {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow:
                0 12px 35px rgba(0, 0, 0, 0.15),
                0 6px 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .content-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
            text-align: center;
        }

        .content-description {
            font-size: 1.1rem;
            color: #666;
            text-align: center;
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        /* 입력 폼 */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
        }

        .form-input:focus {
            outline: none;
            border-color: #6f2dff;
            box-shadow: 0 0 0 3px rgba(111, 45, 255, 0.1);
        }

        .help-text {
            font-size: 0.9rem;
            color: #999;
            margin-top: 0.5rem;
            line-height: 1.5;
        }

        .help-text a {
            color: #6f2dff;
            text-decoration: none;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        /* 정보 박스 */
        .info-box {
            background: linear-gradient(145deg, #f0f4ff, #e8eeff);
            border-left: 4px solid #6f2dff;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .info-box h3 {
            color: #6f2dff;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .info-box p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .info-box ol {
            margin-left: 1.5rem;
            color: #666;
            line-height: 1.8;
        }

        /* 버튼 */
        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(145deg, #6f2dff, #5a1fd9);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(111, 45, 255, 0.4);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        /* 반응형 */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .header-left,
            .header-center,
            .header-right {
                width: 100%;
                justify-content: center;
            }

            .header h1 {
                font-size: 1.3rem;
                text-align: center;
            }

            .menu-nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem;
            }

            .menu-nav a {
                font-size: 0.85rem;
                padding: 0.4rem 0.8rem;
                white-space: nowrap;
            }

            .header-right {
                justify-content: center;
            }

            .container {
                padding: 1rem;
            }

            .content-area {
                padding: 1.5rem;
            }

            .content-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- 헤더 - 전체 너비 -->
    <div class="header">
        <div class="header-container">
            <!-- 왼쪽: 로고 -->
            <div class="header-left">
                <h1 onclick="window.location.href='index.html'">🍌 Bbanana.ai</h1>
            </div>

            <!-- 가운데: 메뉴 -->
            <div class="header-center">
                <nav class="menu-nav">
                    <a href="channelapi.php" class="active">🤖 API 설정</a>
                    <a href="#">📱 AI 샘플즈</a>
                    <a href="#">💬 AI 자유톡</a>
                    <a href="#">📋 AI 보드</a>
                    <a href="#">✍️ AI 랜드카피</a>
                    <a href="index.html">🎬 크레에이터</a>
                </nav>
            </div>

            <!-- 오른쪽: 크레딧, 로그아웃 -->
            <div class="header-right">
                <button class="login-btn" onclick="alert('크레딧 페이지')">💰 크레딧</button>
                <button class="login-btn" onclick="alert('로그아웃')">로그아웃</button>
            </div>
        </div>
    </div>

    <!-- 메인 컨테이너 -->
    <div class="container">
        <!-- 메인 콘텐츠 영역 -->
        <div class="content-area">
            <h2 class="content-title">🔑 Google AI Studio API 설정</h2>
            <p class="content-description">Google AI Studio에서 발급받은 API 키를 등록하세요</p>

            <!-- 정보 박스 -->
            <div class="info-box">
                <h3>📌 API 키 발급 방법</h3>
                <ol>
                    <li><a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>에 접속하세요</li>
                    <li>로그인 후 "Get API Key" 버튼을 클릭하세요</li>
                    <li>"Create API Key" 를 선택하여 새 API 키를 생성하세요</li>
                    <li>생성된 API 키를 복사하여 아래에 입력하세요</li>
                </ol>
            </div>

            <!-- API 키 입력 폼 -->
            <form id="apiForm" onsubmit="saveApiKey(event)">
                <div class="form-group">
                    <label class="form-label" for="apiKey">API 키</label>
                    <input
                        type="text"
                        class="form-input"
                        id="apiKey"
                        name="apiKey"
                        placeholder="AIza..."
                        required
                    >
                    <p class="help-text">
                        API 키는 안전하게 암호화되어 저장됩니다.
                        <a href="https://aistudio.google.com/app/apikey" target="_blank">API 키 발급받기 →</a>
                    </p>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    저장하고 시작하기 →
                </button>
            </form>
        </div>
    </div>

    <script>
        // API 키 저장 함수
        function saveApiKey(event) {
            event.preventDefault();

            const apiKey = document.getElementById('apiKey').value.trim();

            if (!apiKey) {
                alert('API 키를 입력해주세요');
                return;
            }

            // API 키 유효성 검사 (간단한 체크)
            if (!apiKey.startsWith('AIza')) {
                alert('올바른 Google AI Studio API 키 형식이 아닙니다.\nAPI 키는 "AIza"로 시작해야 합니다.');
                return;
            }

            // 버튼 비활성화
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = '저장 중...';

            // 실제로는 서버에 저장하는 로직이 필요합니다
            // 여기서는 localStorage에 임시 저장
            localStorage.setItem('googleAiApiKey', apiKey);

            // 저장 완료 후 크레에이터 페이지로 이동
            setTimeout(() => {
                alert('API 키가 성공적으로 저장되었습니다!');
                window.location.href = 'index.html';
            }, 500);
        }

        // 페이지 로드 시 저장된 API 키가 있으면 표시
        window.addEventListener('DOMContentLoaded', () => {
            const savedApiKey = localStorage.getItem('googleAiApiKey');
            if (savedApiKey) {
                document.getElementById('apiKey').value = savedApiKey;
            }
        });
    </script>
</body>
</html>
