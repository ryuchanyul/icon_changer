<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI 자동화 - Bbanana.ai</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #fff 0%, #fff 100%);
            min-height: 100vh;
        }

        /* 헤더 - 전체 너비 */
        .header {
            background-color: #eee;
            border-bottom: 1px solid #e0e0e0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px 20px;
            gap: 20px;
        }

        .header-left h1 {
            margin: 0;
            font-size: 20px;
            white-space: nowrap;
        }

        .header-center {
            flex: 1;
            display: flex;
            justify-content: center;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .header-right {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .menu-nav {
            display: flex;
            gap: 10px;
            white-space: nowrap;
        }

        .menu-nav a {
            padding: 8px 12px;
            font-size: 14px;
            text-decoration: none;
            color: #333;
            border-radius: 25px;
            transition: all 0.3s;
        }

        .menu-nav a:hover {
            background-color: #f5f5f5;
        }

        .menu-nav a.active {
            background-color: #FFD93D;
            font-weight: 600;
        }

        .login-btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background-color: white;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .login-btn:hover {
            background-color: #f5f5f5;
        }

        /* 컨테이너 */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* 뷰 컨테이너 */
        .view-container {
            display: none;
        }

        .view-container.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        /* 단계별 프로세스 */
        .process-steps {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 20px 10px;
            margin-bottom: 2rem;
        }

        .process-steps::before {
            content: "";
            position: absolute;
            top: 38px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
        }

        .step-item {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            min-width: 80px;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #d1d5db;
            color: #64748b;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        .step-item.active .step-circle {
            background: #6366f1;
            border-color: #6366f1;
            color: #ffffff;
        }

        .step-item.completed .step-circle {
            background: #6366f1;
            border-color: #6366f1;
            color: #ffffff;
        }

        .step-label {
            margin-top: 6px;
            font-size: 13px;
            color: #6b7280;
        }

        .step-item.active .step-label {
            color: #6366f1;
            font-weight: 600;
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
            border-radius: 50px;
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

        /* 프로젝트 페이지 헤더 */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            color: #000;
        }

        .page-header-left h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 0.5rem;
        }

        .page-header-left p {
            font-size: 1rem;
            color: #000;
        }

        .page-header-right {
            display: flex;
            gap: 0.75rem;
            color: #000;
            align-items: center;
        }

        .header-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-btn-settings {
            background: #000;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .header-btn-settings:hover {
            background: #333;
        }

        .header-btn-beta {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .header-btn-beta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
        }

        .header-btn-new {
            background: linear-gradient(135deg, #6f2dff, #5a1fd9);
            color: white;
        }

        .header-btn-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(111, 45, 255, 0.4);
        }

        /* 프로젝트 카드 */
        .project-card {
            background: #1a1a1a;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
            border-color: rgba(111, 45, 255, 0.3);
        }

        .project-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .project-status {
            background: rgba(111, 45, 255, 0.2);
            color: #a78bfa;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .project-status.completed {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .delete-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            padding: 0.25rem;
            transition: color 0.3s;
            font-size: 1rem;
        }

        .delete-btn:hover {
            color: #ef4444;
        }

        .project-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .project-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .project-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .project-step {
            color: rgba(255, 255, 255, 0.8);
        }

        .edit-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.3s;
        }

        .edit-link:hover {
            color: #6f2dff;
        }

        /* 새 영상 만들기 카드 */
        .new-project-card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px dashed rgba(0, 0, 0, 0.2);
            border-radius: 16px;
            padding: 3rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            min-height: 200px;
        }

        .new-project-card:hover {
            background: rgba(111, 45, 255, 0.05);
            border-color: rgba(111, 45, 255, 0.5);
            transform: translateY(-5px);
        }

        .new-project-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(111, 45, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 2rem;
            color: #6f2dff;
        }

        .new-project-text {
            color: #000;
            font-size: 1rem;
            font-weight: 600;
        }

        /* 하단 메시지 */
        .footer-message {
            text-align: center;
            color: rgba(0, 0, 0, 0.5);
            font-size: 0.9rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* 프로젝트 이름 입력 모달 */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .modal-buttons .btn {
            flex: 1;
        }

        /* 반응형 */
        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                gap: 1.5rem;
            }

            .page-header-right {
                width: 100%;
                flex-wrap: wrap;
            }

            .header-btn {
                flex: 1;
                justify-content: center;
            }

            .header-container {
                flex-direction: column;
                gap: 10px;
            }

            .menu-nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .process-steps {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .content-area {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- 헤더 -->
    <div class="header">
        <div class="header-container">
            <div class="header-left">
                <h1><a href="index.php">🍌 Bbanana.ai</a></h1>
            </div>

            <div class="header-center">
                <nav class="menu-nav">
                    <a href="channelapi.php">🤖 API 설정</a>
                    <a href="channelcreationmanager.php">📱 채널시작 도우미</a>
                    <a href="createaiautomation.php" class="active">💬 AI 자동화</a>
                    <a href="#">📋 AI 보드</a>
                    <a href="#">✍️ AI 랜드카피</a>
                    <a href="#">🎬 크리에이터</a>
                </nav>
            </div>

            <div class="header-right">
                <button class="login-btn" onclick="alert('크레딧 페이지')">💰 크레딧</button>
                <button class="login-btn" onclick="alert('로그아웃')">로그아웃</button>
            </div>
        </div>
    </div>

    <!-- 메인 컨테이너 -->
    <div class="container">
        <!-- 프로젝트 목록 뷰 -->
        <div id="projectListView" class="view-container active">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>내 프로젝트</h1>
                    <p>영상 자동화 프로젝트를 관리하세요</p>
                </div>
                <div class="page-header-right">
                    <button class="header-btn header-btn-settings" onclick="openSettings()">
                        ⚙️ 설정
                    </button>
                    <button class="header-btn header-btn-beta" onclick="alert('오토파일럿 기능 준비중입니다!')">
                        🏪 ✨ 오토파일럿
                    </button>
                    <button class="header-btn header-btn-new" onclick="openNewProjectModal()">
                        ➕ 새 프로젝트
                    </button>
                </div>
            </div>

            <!-- 프로젝트 그리드 -->
            <div class="grid" id="projectGrid">
                <!-- 새 영상 만들기 카드 (항상 첫 번째) -->
                <div class="new-project-card" onclick="openNewProjectModal()">
                    <div class="new-project-icon">➕</div>
                    <div class="new-project-text">새 영상 만들기</div>
                </div>
                <!-- 프로젝트 카드들이 여기에 동적으로 추가됩니다 -->
            </div>

            <div class="footer-message">
                모든 프로젝트를 불러왔습니다
            </div>
        </div>

        <!-- 워크플로우 뷰 -->
        <div id="workflowView" class="view-container">
            <!-- 뒤로가기 버튼 -->
            <div style="margin-bottom: 1rem;">
                <button class="btn btn-secondary" onclick="showProjectList()" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                    ← 프로젝트 목록
                </button>
                <span id="currentProjectName" style="margin-left: 1rem; font-weight: 600; color: #333;"></span>
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

                    <div class="options-grid" id="ratioOptions">
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
                        <div class="options-grid" id="styleOptions" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
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
                        <button class="btn btn-secondary" onclick="showProjectList()">← 대시보드</button>
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
                        <label class="form-label">생성된 대본</label>
                        <textarea class="form-textarea" id="generatedScript" placeholder="AI가 생성한 대본이 여기에 표시됩니다..."></textarea>
                    </div>

                    <button class="btn btn-primary" onclick="generateScript()" style="margin-bottom: 1rem; width: 100%;">
                        🤖 AI 대본 생성
                    </button>

                    <div class="button-group">
                        <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                        <button class="btn btn-primary" onclick="nextStep()">다음 단계 →</button>
                    </div>
                </div>

                <!-- 3단계: 음성 생성 -->
                <div class="step-content" data-step="3">
                    <h2 class="content-title">음성 생성</h2>
                    <p class="content-description">AI 보이스를 선택하고 음성을 생성하세요</p>

                    <div class="form-group">
                        <label class="form-label">AI 보이스 선택</label>
                        <div class="options-grid" id="voiceOptions" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
                            <div class="option-card" onclick="selectVoice('female1', this)">
                                <div class="option-icon">👩</div>
                                <div class="option-title">여성 1</div>
                                <div class="option-description">밝고 친근한</div>
                            </div>
                            <div class="option-card" onclick="selectVoice('female2', this)">
                                <div class="option-icon">👩‍💼</div>
                                <div class="option-title">여성 2</div>
                                <div class="option-description">차분하고 전문적인</div>
                            </div>
                            <div class="option-card" onclick="selectVoice('male1', this)">
                                <div class="option-icon">👨</div>
                                <div class="option-title">남성 1</div>
                                <div class="option-description">깊고 신뢰감 있는</div>
                            </div>
                            <div class="option-card" onclick="selectVoice('male2', this)">
                                <div class="option-icon">👨‍💼</div>
                                <div class="option-title">남성 2</div>
                                <div class="option-description">에너지 넘치는</div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" onclick="generateVoice()" style="margin-bottom: 1rem; width: 100%;">
                        🎤 음성 생성
                    </button>

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
                        <div class="options-grid" id="imageStyleOptions" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
                            <div class="option-card" onclick="selectImageStyle('realistic', this)">
                                <div class="option-icon">📷</div>
                                <div class="option-title">사실적</div>
                            </div>
                            <div class="option-card" onclick="selectImageStyle('anime', this)">
                                <div class="option-icon">🎨</div>
                                <div class="option-title">애니메이션</div>
                            </div>
                            <div class="option-card" onclick="selectImageStyle('cartoon', this)">
                                <div class="option-icon">🖼️</div>
                                <div class="option-title">카툰</div>
                            </div>
                            <div class="option-card" onclick="selectImageStyle('3d', this)">
                                <div class="option-icon">🎮</div>
                                <div class="option-title">3D</div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" onclick="generateImages()" style="margin-bottom: 1rem; width: 100%;">
                        🖼️ 이미지 생성
                    </button>

                    <div class="button-group">
                        <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                        <button class="btn btn-primary" onclick="nextStep()">다음 단계 →</button>
                    </div>
                </div>

                <!-- 5단계: 영상 생성 -->
                <div class="step-content" data-step="5">
                    <h2 class="content-title">영상 생성</h2>
                    <p class="content-description">모든 요소를 합쳐 영상을 생성합니다</p>

                    <div style="text-align: center; padding: 3rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">🎬</div>
                        <p style="color: #666; margin-bottom: 2rem;">대본, 음성, 이미지를 조합하여 영상을 생성합니다</p>
                        <button class="btn btn-primary" onclick="generateVideo()" style="width: auto;">
                            🚀 영상 생성 시작
                        </button>
                    </div>

                    <div class="button-group">
                        <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                        <button class="btn btn-primary" onclick="nextStep()">다음 단계 →</button>
                    </div>
                </div>

                <!-- 6단계: 영상 편집기 -->
                <div class="step-content" data-step="6">
                    <h2 class="content-title">영상 편집기</h2>
                    <p class="content-description">생성된 영상을 미리보고 편집하세요</p>

                    <div style="background: #1a1a1a; border-radius: 12px; padding: 2rem; text-align: center; margin-bottom: 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">🎥</div>
                        <p style="color: #999;">영상 미리보기 영역</p>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 2rem;">
                        <button class="btn btn-secondary" onclick="alert('다운로드 준비중')">⬇️ 다운로드</button>
                        <button class="btn btn-primary" onclick="alert('유튜브 업로드 준비중')">📤 유튜브 업로드</button>
                    </div>

                    <div class="button-group">
                        <button class="btn btn-secondary" onclick="prevStep()">← 이전</button>
                        <button class="btn btn-primary" onclick="completeProject()">✅ 프로젝트 완료</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 새 프로젝트 모달 -->
    <div class="modal-overlay" id="newProjectModal">
        <div class="modal-content">
            <h2 class="modal-title">새 프로젝트 만들기</h2>
            <div class="form-group">
                <label class="form-label">프로젝트 이름</label>
                <input type="text" class="form-input" id="newProjectName" placeholder="프로젝트 이름을 입력하세요">
            </div>
            <div class="modal-buttons">
                <button class="btn btn-secondary" onclick="closeNewProjectModal()">취소</button>
                <button class="btn btn-primary" onclick="createNewProject()">만들기</button>
            </div>
        </div>
    </div>

    <script>
        // ==========================================
        // 전역 변수
        // ==========================================
        let currentStep = 1;
        let currentProjectId = null;
        let projects = [];

        // 프로젝트 데이터 구조
        const defaultProjectData = {
            ratio: null,
            style: null,
            topic: '',
            keywords: '',
            script: '',
            voice: null,
            imageStyle: null,
            step: 1,
            createdAt: null,
            updatedAt: null
        };

        // ==========================================
        // 초기화
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('AI 자동화 페이지 로드됨');
            loadProjects();
            renderProjectList();

            // URL 파라미터 체크
            const urlParams = new URLSearchParams(window.location.search);
            const projectId = urlParams.get('project');

            if (projectId) {
                editProject(projectId);
            }
        });

        // ==========================================
        // 프로젝트 저장/로드
        // ==========================================
        function loadProjects() {
            const saved = localStorage.getItem('aiAutomationProjects');
            if (saved) {
                projects = JSON.parse(saved);
            }
            console.log('프로젝트 로드됨:', projects.length + '개');
        }

        function saveProjects() {
            localStorage.setItem('aiAutomationProjects', JSON.stringify(projects));
            console.log('프로젝트 저장됨:', projects.length + '개');
        }

        // ==========================================
        // 프로젝트 목록 렌더링
        // ==========================================
        function renderProjectList() {
            const grid = document.getElementById('projectGrid');

            // 새 영상 만들기 카드 유지
            let html = `
                <div class="new-project-card" onclick="openNewProjectModal()">
                    <div class="new-project-icon">➕</div>
                    <div class="new-project-text">새 영상 만들기</div>
                </div>
            `;

            // 프로젝트 카드 추가 (최신순)
            const sortedProjects = [...projects].sort((a, b) =>
                new Date(b.updatedAt || b.createdAt) - new Date(a.updatedAt || a.createdAt)
            );

            sortedProjects.forEach(project => {
                const timeDiff = getTimeDiff(project.updatedAt || project.createdAt);
                const statusClass = project.step >= 6 ? 'completed' : '';
                const statusText = project.step >= 6 ? '완료' : '작성 중';

                html += `
                    <div class="project-card" onclick="editProject('${project.id}')">
                        <div class="project-card-header">
                            <span class="project-status ${statusClass}">${statusText}</span>
                            <button class="delete-btn" onclick="deleteProject(event, '${project.id}')">🗑️</button>
                        </div>
                        <h3 class="project-title">${escapeHtml(project.name)}</h3>
                        <div class="project-footer">
                            <div class="project-info">
                                <span>⏱️ ${timeDiff}</span>
                                <span class="project-step">Step ${project.step}/6</span>
                            </div>
                            <a href="#" class="edit-link" onclick="event.stopPropagation(); editProject('${project.id}')">편집 →</a>
                        </div>
                    </div>
                `;
            });

            grid.innerHTML = html;
        }

        // ==========================================
        // 시간 차이 계산
        // ==========================================
        function getTimeDiff(dateString) {
            if (!dateString) return '방금 전';

            const now = new Date();
            const date = new Date(dateString);
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return '방금 전';
            if (diffMins < 60) return `${diffMins}분 전`;
            if (diffHours < 24) return `${diffHours}시간 전`;
            return `${diffDays}일 전`;
        }

        // ==========================================
        // 새 프로젝트 모달
        // ==========================================
        function openNewProjectModal() {
            document.getElementById('newProjectModal').classList.add('active');
            document.getElementById('newProjectName').value = '';
            document.getElementById('newProjectName').focus();
        }

        function closeNewProjectModal() {
            document.getElementById('newProjectModal').classList.remove('active');
        }

        // ==========================================
        // 새 프로젝트 생성
        // ==========================================
        function createNewProject() {
            const nameInput = document.getElementById('newProjectName');
            const name = nameInput.value.trim() || '새 프로젝트';

            const newProject = {
                id: 'proj_' + Date.now(),
                name: name,
                ...defaultProjectData,
                createdAt: new Date().toISOString(),
                updatedAt: new Date().toISOString()
            };

            projects.push(newProject);
            saveProjects();
            closeNewProjectModal();

            console.log('새 프로젝트 생성:', newProject);

            // 바로 편집 모드로 이동
            editProject(newProject.id);
        }

        // ==========================================
        // 프로젝트 편집
        // ==========================================
        function editProject(projectId) {
            const project = projects.find(p => p.id === projectId);

            if (!project) {
                alert('프로젝트를 찾을 수 없습니다.');
                return;
            }

            currentProjectId = projectId;
            currentStep = project.step || 1;

            console.log('프로젝트 편집:', project);

            // 프로젝트 이름 표시
            document.getElementById('currentProjectName').textContent = project.name;

            // 저장된 데이터 복원
            restoreProjectData(project);

            // 워크플로우 뷰로 전환
            showWorkflowView();
            goToStep(currentStep);

            // URL 업데이트
            history.pushState({}, '', `?project=${projectId}`);
        }

        // ==========================================
        // 프로젝트 데이터 복원
        // ==========================================
        function restoreProjectData(project) {
            // 비율 선택 복원
            if (project.ratio) {
                const ratioCards = document.querySelectorAll('#ratioOptions .option-card');
                ratioCards.forEach(card => {
                    card.classList.remove('selected');
                    if (card.querySelector('.option-title').textContent === project.ratio) {
                        card.classList.add('selected');
                    }
                });
            }

            // 스타일 선택 복원
            if (project.style) {
                const styleCards = document.querySelectorAll('#styleOptions .option-card');
                styleCards.forEach(card => {
                    card.classList.remove('selected');
                    if (card.querySelector('.option-title').textContent.toLowerCase().includes(project.style)) {
                        card.classList.add('selected');
                    }
                });
            }

            // 대본 관련 데이터 복원
            if (project.topic) document.getElementById('scriptTopic').value = project.topic;
            if (project.keywords) document.getElementById('scriptKeywords').value = project.keywords;
            if (project.script) document.getElementById('generatedScript').value = project.script;
        }

        // ==========================================
        // 프로젝트 삭제
        // ==========================================
        function deleteProject(event, projectId) {
            event.stopPropagation();

            const project = projects.find(p => p.id === projectId);
            if (!project) return;

            if (confirm(`"${project.name}" 프로젝트를 삭제하시겠습니까?\n삭제된 프로젝트는 복구할 수 없습니다.`)) {
                projects = projects.filter(p => p.id !== projectId);
                saveProjects();
                renderProjectList();

                console.log('프로젝트 삭제됨:', projectId);
            }
        }

        // ==========================================
        // 현재 프로젝트 저장
        // ==========================================
        function saveCurrentProject() {
            if (!currentProjectId) return;

            const projectIndex = projects.findIndex(p => p.id === currentProjectId);
            if (projectIndex === -1) return;

            // 현재 데이터 수집
            projects[projectIndex] = {
                ...projects[projectIndex],
                step: currentStep,
                topic: document.getElementById('scriptTopic')?.value || '',
                keywords: document.getElementById('scriptKeywords')?.value || '',
                script: document.getElementById('generatedScript')?.value || '',
                updatedAt: new Date().toISOString()
            };

            saveProjects();
            console.log('프로젝트 자동 저장됨');
        }

        // ==========================================
        // 뷰 전환
        // ==========================================
        function showProjectList() {
            saveCurrentProject();

            document.getElementById('projectListView').classList.add('active');
            document.getElementById('workflowView').classList.remove('active');

            renderProjectList();

            // URL 업데이트
            history.pushState({}, '', window.location.pathname);
            currentProjectId = null;
        }

        function showWorkflowView() {
            document.getElementById('projectListView').classList.remove('active');
            document.getElementById('workflowView').classList.add('active');
        }

        // ==========================================
        // 단계 이동
        // ==========================================
        function goToStep(step) {
            if (step < 1 || step > 6) return;

            saveCurrentProject();
            currentStep = step;

            // 단계 UI 업데이트
            const stepItems = document.querySelectorAll('.step-item');
            stepItems.forEach((item, index) => {
                item.classList.remove('active', 'completed');
                if (index + 1 < step) {
                    item.classList.add('completed');
                } else if (index + 1 === step) {
                    item.classList.add('active');
                }
            });

            // 콘텐츠 UI 업데이트
            const stepContents = document.querySelectorAll('.step-content');
            stepContents.forEach(content => {
                content.classList.remove('active');
                if (parseInt(content.dataset.step) === step) {
                    content.classList.add('active');
                }
            });

            // 프로젝트 step 업데이트
            if (currentProjectId) {
                const projectIndex = projects.findIndex(p => p.id === currentProjectId);
                if (projectIndex !== -1 && projects[projectIndex].step < step) {
                    projects[projectIndex].step = step;
                    saveProjects();
                }
            }

            console.log('단계 이동:', step);
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

        // ==========================================
        // 옵션 선택
        // ==========================================
        function selectRatio(ratio, element) {
            const cards = element.parentElement.querySelectorAll('.option-card');
            cards.forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');

            if (currentProjectId) {
                const projectIndex = projects.findIndex(p => p.id === currentProjectId);
                if (projectIndex !== -1) {
                    projects[projectIndex].ratio = ratio;
                    saveProjects();
                }
            }

            console.log('비율 선택:', ratio);
        }

        function selectStyle(style, element) {
            const cards = element.parentElement.querySelectorAll('.option-card');
            cards.forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');

            if (currentProjectId) {
                const projectIndex = projects.findIndex(p => p.id === currentProjectId);
                if (projectIndex !== -1) {
                    projects[projectIndex].style = style;
                    saveProjects();
                }
            }

            console.log('스타일 선택:', style);
        }

        function selectVoice(voice, element) {
            const cards = element.parentElement.querySelectorAll('.option-card');
            cards.forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');

            if (currentProjectId) {
                const projectIndex = projects.findIndex(p => p.id === currentProjectId);
                if (projectIndex !== -1) {
                    projects[projectIndex].voice = voice;
                    saveProjects();
                }
            }

            console.log('보이스 선택:', voice);
        }

        function selectImageStyle(imageStyle, element) {
            const cards = element.parentElement.querySelectorAll('.option-card');
            cards.forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');

            if (currentProjectId) {
                const projectIndex = projects.findIndex(p => p.id === currentProjectId);
                if (projectIndex !== -1) {
                    projects[projectIndex].imageStyle = imageStyle;
                    saveProjects();
                }
            }

            console.log('이미지 스타일 선택:', imageStyle);
        }

        // ==========================================
        // AI 기능 (시뮬레이션)
        // ==========================================
        function generateScript() {
            const topic = document.getElementById('scriptTopic').value;
            const keywords = document.getElementById('scriptKeywords').value;

            if (!topic) {
                alert('주제를 입력해주세요.');
                return;
            }

            // 로딩 시뮬레이션
            const textarea = document.getElementById('generatedScript');
            textarea.value = '대본을 생성하는 중...';

            setTimeout(() => {
                textarea.value = `[AI 생성 대본]\n\n주제: ${topic}\n키워드: ${keywords || '없음'}\n\n안녕하세요, 오늘은 "${topic}"에 대해 알아보겠습니다.\n\n${keywords ? `특히 ${keywords.split(',').join(', ')} 등에 대해 자세히 다뤄볼 예정입니다.\n\n` : ''}이 영상에서는 핵심적인 내용을 쉽게 설명해드리겠습니다.\n\n[본론]\n\n1. 첫 번째 포인트\n   - 상세 설명\n\n2. 두 번째 포인트\n   - 상세 설명\n\n3. 세 번째 포인트\n   - 상세 설명\n\n[결론]\n\n오늘 알아본 내용을 정리하면...\n\n시청해주셔서 감사합니다. 좋아요와 구독 부탁드립니다!`;

                saveCurrentProject();
                console.log('대본 생성 완료');
            }, 1500);
        }

        function generateVoice() {
            const script = document.getElementById('generatedScript')?.value;

            if (!script || script.includes('생성하는 중')) {
                alert('먼저 대본을 생성해주세요.');
                return;
            }

            alert('🎤 음성 생성이 시작되었습니다.\n(실제 환경에서는 AI TTS API가 호출됩니다)');
            console.log('음성 생성 요청');
        }

        function generateImages() {
            alert('🖼️ 이미지 생성이 시작되었습니다.\n(실제 환경에서는 AI 이미지 생성 API가 호출됩니다)');
            console.log('이미지 생성 요청');
        }

        function generateVideo() {
            alert('🎬 영상 생성이 시작되었습니다.\n(실제 환경에서는 영상 합성 처리가 진행됩니다)');
            console.log('영상 생성 요청');
        }

        function completeProject() {
            if (currentProjectId) {
                const projectIndex = projects.findIndex(p => p.id === currentProjectId);
                if (projectIndex !== -1) {
                    projects[projectIndex].step = 6;
                    saveProjects();
                }
            }

            alert('✅ 프로젝트가 완료되었습니다!');
            showProjectList();
        }

        // ==========================================
        // 유틸리티
        // ==========================================
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function openSettings() {
            alert('⚙️ 설정 기능 준비중입니다.');
        }

        // 모달 외부 클릭 시 닫기
        document.getElementById('newProjectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNewProjectModal();
            }
        });

        // Enter 키로 프로젝트 생성
        document.getElementById('newProjectName').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                createNewProject();
            }
        });

        // 브라우저 뒤로가기 처리
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const projectId = urlParams.get('project');

            if (projectId) {
                editProject(projectId);
            } else {
                showProjectList();
            }
        });
    </script>
</body>
</html>
