<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>채널 생성 매니저 - 유튜브 채널관리</title>
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
            padding: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* 헤더 */
        .header {
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
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
            gap: 2rem;
        }

        .header-left {
            flex-shrink: 0;
        }

        .header h1 {
            color: #6f2dff;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
        }

        .header-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .menu-nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .menu-nav a {
            color: #666;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
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

        .user-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .login-btn {
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, #6f2dff 0%, #5a1fd9 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(111, 45, 255, 0.3);
            white-space: nowrap;
            font-size: 0.9rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(111, 45, 255, 0.4);
        }

        /* 단계별 프로세스 */
        .process-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
            position: relative;
        }

        .process-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: linear-gradient(90deg, #e0e0e0 0%, #e0e0e0 100%);
            z-index: 0;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 1;
            cursor: pointer;
            transition: all 0.3s;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            border: 3px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #999;
            font-size: 1rem;
            box-shadow:
                0 4px 8px rgba(0, 0, 0, 0.1),
                inset 0 1px 2px rgba(255, 255, 255, 0.5);
            transition: all 0.3s;
        }

        .step-item.active .step-circle {
            background: linear-gradient(145deg, #6f2dff, #5a1fd9);
            border-color: #6f2dff;
            color: white;
            box-shadow:
                0 6px 15px rgba(111, 45, 255, 0.4),
                inset 0 -2px 4px rgba(0, 0, 0, 0.2),
                inset 0 2px 4px rgba(150, 100, 255, 0.3);
            transform: scale(1.1);
        }

        .step-item.completed .step-circle {
            background: linear-gradient(145deg, #4CAF50, #45a049);
            border-color: #4CAF50;
            color: white;
        }

        .step-label {
            font-size: 0.85rem;
            color: #999;
            font-weight: 600;
            white-space: nowrap;
        }

        .step-item.active .step-label {
            color: #6f2dff;
        }

        .step-item.completed .step-label {
            color: #4CAF50;
        }

        /* 메인 콘텐츠 영역 */
        .content-area {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow:
                0 12px 35px rgba(0, 0, 0, 0.15),
                0 6px 15px rgba(0, 0, 0, 0.1);
            min-height: 500px;
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
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

        .form-textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            min-height: 150px;
            resize: vertical;
            font-family: inherit;
            transition: all 0.3s;
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
        }

        .form-textarea:focus {
            outline: none;
            border-color: #6f2dff;
            box-shadow: 0 0 0 3px rgba(111, 45, 255, 0.1);
        }

        /* 옵션 그리드 */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .option-card {
            background: linear-gradient(145deg, #f9f9f9, #f0f0f0);
            border: 2px solid #e0e0e0;
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #6f2dff;
        }

        .option-card.selected {
            background: linear-gradient(145deg, #6f2dff, #5a1fd9);
            border-color: #6f2dff;
            color: white;
            box-shadow: 0 8px 20px rgba(111, 45, 255, 0.3);
        }

        .option-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .option-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .option-description {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* 버튼 그룹 */
        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 3rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(145deg, #6f2dff, #5a1fd9);
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(111, 45, 255, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(145deg, #e0e0e0, #d0d0d0);
            color: #666;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        /* 결과 표시 */
        .result-item {
            background: linear-gradient(145deg, #f9f9f9, #f0f0f0);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .result-item h3 {
            color: #6f2dff;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .result-item p {
            color: #666;
            line-height: 1.6;
        }

        /* 반응형 */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

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

            .content-area {
                padding: 1.5rem;
            }

            .content-title {
                font-size: 1.5rem;
            }

            .button-group {
                flex-direction: column;
            }

            .process-steps {
                gap: 0.5rem;
                overflow-x: auto;
                padding: 1rem 0;
            }

            .step-circle {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }

            .step-label {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 헤더 -->
        <div class="header">
            <div class="header-container">
                <!-- 왼쪽: 로고 -->
                <div class="header-left">
                    <h1>🍌 Bbanana.ai</h1>
                </div>

                <!-- 가운데: 메뉴 -->
                <div class="header-center">
                    <nav class="menu-nav">
                        <a href="index.html">🤖 AI 설정</a>
                        <a href="#">📱 AI 샘플즈</a>
                        <a href="#">💬 AI 자유톡</a>
                        <a href="#">📋 AI 보드</a>
                        <a href="#">✍️ AI 랜드카피</a>
                        <a href="#" class="active">🎬 크레에이터</a>
                    </nav>
                </div>

                <!-- 오른쪽: 크레딧, 메인, 로그아웃 -->
                <div class="header-right">
                    <button class="login-btn" onclick="alert('크레딧 페이지')">💰 크레딧</button>
                    <button class="login-btn" onclick="window.location.href='index.html'">◀ 메인</button>
                    <button class="login-btn" onclick="alert('로그아웃')">로그아웃</button>
                </div>
            </div>
        </div>

        <!-- 단계별 프로세스 -->
        <div class="process-steps">
            <div class="step-item active" onclick="goToStep(1)">
                <div class="step-circle">1</div>
                <div class="step-label">설정</div>
            </div>
            <div class="step-item" onclick="goToStep(2)">
                <div class="step-circle">2</div>
                <div class="step-label">대본 생성</div>
            </div>
            <div class="step-item" onclick="goToStep(3)">
                <div class="step-circle">3</div>
                <div class="step-label">음성 생성</div>
            </div>
            <div class="step-item" onclick="goToStep(4)">
                <div class="step-circle">4</div>
                <div class="step-label">이미지 생성</div>
            </div>
            <div class="step-item" onclick="goToStep(5)">
                <div class="step-circle">5</div>
                <div class="step-label">영상 생성</div>
            </div>
            <div class="step-item" onclick="goToStep(6)">
                <div class="step-circle">6</div>
                <div class="step-label">영상 편집기</div>
            </div>
        </div>

        <!-- 메인 콘텐츠 영역 -->
        <div class="content-area">
            <!-- 1단계: 설정 -->
            <div class="step-content active" data-step="1">
                <h2 class="content-title">영상 비율 선택</h2>
                <p class="content-description">소셜미디어 비율을 선택하고 시작해주세요</p>

                <div class="options-grid">
                    <div class="option-card" onclick="selectRatio('16:9', this)">
                        <div class="option-icon">📺</div>
                        <div class="option-title">16:9</div>
                        <div class="option-description">유튜브, 데스크톱</div>
                    </div>
                    <div class="option-card" onclick="selectRatio('1:1', this)">
                        <div class="option-icon">□</div>
                        <div class="option-title">1:1</div>
                        <div class="option-description">인스타그램, 포스트</div>
                    </div>
                    <div class="option-card" onclick="selectRatio('3:4', this)">
                        <div class="option-icon">📱</div>
                        <div class="option-title">3:4</div>
                        <div class="option-description">인스타그램, 릴스 (BETA)</div>
                    </div>
                    <div class="option-card" onclick="selectRatio('9:16', this)">
                        <div class="option-icon">📲</div>
                        <div class="option-title">9:16</div>
                        <div class="option-description">모바일, 릴스, 쇼츠</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">스타일 선택</label>
                    <div class="options-grid">
                        <div class="option-card" onclick="selectStyle('minimalist', this)">
                            <div class="option-title">미니멀리스트</div>
                        </div>
                        <div class="option-card" onclick="selectStyle('modern', this)">
                            <div class="option-title">모던 스타일</div>
                        </div>
                        <div class="option-card" onclick="selectStyle('vintage', this)">
                            <div class="option-title">빈티지</div>
                        </div>
                    </div>
                </div>

                <div class="button-group">
                    <button class="btn btn-secondary" onclick="window.location.href='index.html'">이전</button>
                    <button class="btn btn-primary" onclick="nextStep()">다음 단계 →</button>
                </div>
            </div>

            <!-- 2단계: 대본 생성 -->
            <div class="step-content" data-step="2">
                <h2 class="content-title">대본 생성</h2>
                <p class="content-description">AI가 자동으로 대본을 생성합니다</p>

                <div class="form-group">
                    <label class="form-label">주제 입력</label>
                    <input type="text" class="form-input" placeholder="영상의 주제를 입력하세요" id="scriptTopic">
                </div>

                <div class="form-group">
                    <label class="form-label">키워드</label>
                    <input type="text" class="form-input" placeholder="관련 키워드를 입력하세요 (쉼표로 구분)" id="scriptKeywords">
                </div>

                <div class="form-group">
                    <label class="form-label">대본 스타일</label>
                    <div class="options-grid">
                        <div class="option-card" onclick="selectScriptStyle('informative', this)">
                            <div class="option-title">정보 전달형</div>
                        </div>
                        <div class="option-card" onclick="selectScriptStyle('entertaining', this)">
                            <div class="option-title">엔터테인먼트</div>
                        </div>
                        <div class="option-card" onclick="selectScriptStyle('educational', this)">
                            <div class="option-title">교육용</div>
                        </div>
                    </div>
                </div>

                <div class="button-group">
                    <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                    <button class="btn btn-primary" onclick="generateScript()">대본 생성</button>
                </div>
            </div>

            <!-- 3단계: 음성 생성 -->
            <div class="step-content" data-step="3">
                <h2 class="content-title">음성 생성</h2>
                <p class="content-description">AI 보이스를 선택하고 음성을 생성하세요</p>

                <div class="form-group">
                    <label class="form-label">음성 선택</label>
                    <div class="options-grid">
                        <div class="option-card" onclick="selectVoice('male1', this)">
                            <div class="option-icon">👨</div>
                            <div class="option-title">남성 목소리 1</div>
                        </div>
                        <div class="option-card" onclick="selectVoice('female1', this)">
                            <div class="option-icon">👩</div>
                            <div class="option-title">여성 목소리 1</div>
                        </div>
                        <div class="option-card" onclick="selectVoice('male2', this)">
                            <div class="option-icon">🧑</div>
                            <div class="option-title">남성 목소리 2</div>
                        </div>
                        <div class="option-card" onclick="selectVoice('female2', this)">
                            <div class="option-icon">👧</div>
                            <div class="option-title">여성 목소리 2</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">음성 속도</label>
                    <input type="range" min="0.5" max="2" step="0.1" value="1" class="form-input" id="voiceSpeed">
                </div>

                <div class="button-group">
                    <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                    <button class="btn btn-primary" onclick="nextStep()">다음 단계 →</button>
                </div>
            </div>

            <!-- 4단계: 이미지 생성 -->
            <div class="step-content" data-step="4">
                <h2 class="content-title">이미지 생성</h2>
                <p class="content-description">대본에 맞는 이미지를 AI가 생성합니다</p>

                <div class="form-group">
                    <label class="form-label">이미지 스타일</label>
                    <div class="options-grid">
                        <div class="option-card" onclick="selectImageStyle('realistic', this)">
                            <div class="option-title">사실적</div>
                        </div>
                        <div class="option-card" onclick="selectImageStyle('illustration', this)">
                            <div class="option-title">일러스트</div>
                        </div>
                        <div class="option-card" onclick="selectImageStyle('cartoon', this)">
                            <div class="option-title">만화 스타일</div>
                        </div>
                        <div class="option-card" onclick="selectImageStyle('abstract', this)">
                            <div class="option-title">추상적</div>
                        </div>
                    </div>
                </div>

                <div class="button-group">
                    <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                    <button class="btn btn-primary" onclick="nextStep()">다음 단계 →</button>
                </div>
            </div>

            <!-- 5단계: 영상 생성 -->
            <div class="step-content" data-step="5">
                <h2 class="content-title">영상 생성</h2>
                <p class="content-description">모든 요소를 합쳐 영상을 생성합니다</p>

                <div class="result-item">
                    <h3>✅ 설정 완료</h3>
                    <p>영상 비율, 스타일 선택 완료</p>
                </div>

                <div class="result-item">
                    <h3>✅ 대본 생성 완료</h3>
                    <p>AI가 생성한 대본이 준비되었습니다</p>
                </div>

                <div class="result-item">
                    <h3>✅ 음성 생성 완료</h3>
                    <p>선택한 음성으로 녹음이 완료되었습니다</p>
                </div>

                <div class="result-item">
                    <h3>✅ 이미지 생성 완료</h3>
                    <p>대본에 맞는 이미지가 생성되었습니다</p>
                </div>

                <div class="button-group">
                    <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                    <button class="btn btn-primary" onclick="nextStep()">영상 생성 시작 →</button>
                </div>
            </div>

            <!-- 6단계: 영상 편집기 -->
            <div class="step-content" data-step="6">
                <h2 class="content-title">영상 편집기</h2>
                <p class="content-description">생성된 영상을 미리보고 편집하세요</p>

                <div class="result-item">
                    <h3>🎬 영상 생성 완료!</h3>
                    <p>영상이 성공적으로 생성되었습니다. 편집기에서 최종 조정을 할 수 있습니다.</p>
                </div>

                <div class="button-group">
                    <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                    <button class="btn btn-primary" onclick="downloadVideo()">영상 다운로드</button>
                    <button class="btn btn-primary" onclick="openEditor()">편집기 열기</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let formData = {
            ratio: '',
            style: '',
            scriptStyle: '',
            voice: '',
            imageStyle: ''
        };

        // 단계 이동 함수
        function goToStep(stepNumber) {
            // 현재 단계 숨기기
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });

            // 새 단계 표시
            document.querySelector(`[data-step="${stepNumber}"]`).classList.add('active');

            // 단계 표시 업데이트
            const steps = document.querySelectorAll('.step-item');
            steps.forEach((step, index) => {
                step.classList.remove('active', 'completed');

                if (index + 1 < stepNumber) {
                    step.classList.add('completed');
                }

                if (index + 1 === stepNumber) {
                    step.classList.add('active');
                }
            });

            currentStep = stepNumber;
        }

        function nextStep() {
            if (currentStep < 6) {
                goToStep(currentStep + 1);
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                goToStep(currentStep - 1);
            }
        }

        // 비율 선택
        function selectRatio(ratio, element) {
            document.querySelectorAll('.options-grid .option-card').forEach(card => {
                if (card.parentElement === element.parentElement) {
                    card.classList.remove('selected');
                }
            });
            element.classList.add('selected');
            formData.ratio = ratio;
        }

        // 스타일 선택
        function selectStyle(style, element) {
            document.querySelectorAll('.options-grid .option-card').forEach(card => {
                if (card.parentElement === element.parentElement) {
                    card.classList.remove('selected');
                }
            });
            element.classList.add('selected');
            formData.style = style;
        }

        // 대본 스타일 선택
        function selectScriptStyle(style, element) {
            document.querySelectorAll('[data-step="2"] .option-card').forEach(card => {
                card.classList.remove('selected');
            });
            element.classList.add('selected');
            formData.scriptStyle = style;
        }

        // 음성 선택
        function selectVoice(voice, element) {
            document.querySelectorAll('[data-step="3"] .option-card').forEach(card => {
                card.classList.remove('selected');
            });
            element.classList.add('selected');
            formData.voice = voice;
        }

        // 이미지 스타일 선택
        function selectImageStyle(style, element) {
            document.querySelectorAll('[data-step="4"] .option-card').forEach(card => {
                card.classList.remove('selected');
            });
            element.classList.add('selected');
            formData.imageStyle = style;
        }

        // 대본 생성
        function generateScript() {
            const topic = document.getElementById('scriptTopic').value;
            const keywords = document.getElementById('scriptKeywords').value;

            if (!topic) {
                alert('주제를 입력해주세요');
                return;
            }

            alert('대본을 생성하고 있습니다...');
            setTimeout(() => {
                alert('대본 생성이 완료되었습니다!');
                nextStep();
            }, 1500);
        }

        // 영상 다운로드
        function downloadVideo() {
            alert('영상 다운로드를 시작합니다...');
        }

        // 편집기 열기
        function openEditor() {
            alert('영상 편집기를 여는 중...');
        }
    </script>
</body>
</html>
