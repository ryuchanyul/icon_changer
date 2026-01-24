<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <style>
        a {
            text-decoration: none; /* 링크의 밑줄 제거 */
            color: inherit; /* 링크의 색상 제거 */
        }
    </style>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* === Select 드롭다운 스타일 === */
        #oauthAccountSelect {
            background: white !important;
            color: #000 !important;
            font-weight: 500 !important;
        }

        #oauthAccountSelect option {
            background: white !important;
            background-color: white !important;
            color: #000 !important;
            padding: 10px !important;
            font-weight: 500 !important;
        }

        #oauthAccountSelect option[value=""] {
            color: #666 !important;
        }

        #oauthAccountSelect option:hover {
            background: #e8e8e8 !important;
            background-color: #e8e8e8 !important;
            color: #000 !important;
        }

        #oauthAccountSelect option:checked {
            background: #667eea !important;
            background-color: #667eea !important;
            color: white !important;
            font-weight: 600 !important;
        }

        /* Firefox 전용 */
        @-moz-document url-prefix() {
            #oauthAccountSelect option {
                color: #000 !important;
            }
        }

        /* === 사이드바 === */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 600px;
            background: linear-gradient(180deg, #6f2dff 0%, #5a1fd9 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* API 키 섹션 */
        .api-section {
            margin: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            backdrop-filter: blur(10px);
        }

        .api-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .api-title {
            font-size: 0.9rem;
            font-weight: 600;
            flex: 1;
        }

        .help-btn {
            display: inline-flex;
            width: 22px;
            height: 22px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin-left: 0.5rem;
            vertical-align: middle;
        }

        .help-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .api-input {
            width: 100%;
            padding: 0.6rem 0.8rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.3s;
        }

        .api-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .api-input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .api-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.6rem;
            font-size: 0.75rem;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #10b981;
        }

        .status-dot.inactive {
            background-color: #ef4444;
        }

        .menu-list {
            list-style: none;
            padding: 1rem 0;
        }

        .menu-item {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.95rem;
            position: relative;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-left-color: rgba(255, 255, 255, 0.5);
        }

        .menu-item.active {
            background-color: rgba(111, 45, 255, 0.25);
            font-weight: 600;
            border-left-color: rgba(111, 45, 255, 0.9);
            box-shadow: inset 0 0 20px rgba(111, 45, 255, 0.15);
        }

        .menu-item.active:hover {
            background-color: rgba(111, 45, 255, 0.35);
            border-left-color: rgba(111, 45, 255, 1);
            box-shadow: inset 0 0 25px rgba(111, 45, 255, 0.25);
        }

        .submenu-item {
            padding: 0.65rem 1.5rem 0.65rem 2.5rem;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.8);
            position: relative;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.03) 0%, transparent 100%);
        }

        .submenu-item::before {
            content: '';
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transition: all 0.3s;
        }

        .submenu-item::after {
            content: '';
            position: absolute;
            left: 1.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(255, 255, 255, 0.1);
        }

        .submenu-item:hover {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.03) 100%);
            color: rgba(255, 255, 255, 1);
            padding-left: 2.8rem;
        }

        .submenu-item:hover::before {
            background: rgba(111, 45, 255, 0.8);
            transform: translateY(-50%) scale(1.3);
            box-shadow: 0 0 10px rgba(111, 45, 255, 0.5);
        }

        .submenu-item.active {
            background: linear-gradient(90deg, rgba(111, 45, 255, 0.15) 0%, rgba(111, 45, 255, 0.05) 100%);
            color: rgba(255, 255, 255, 1);
            font-weight: 500;
        }

        .submenu-item.active::before {
            background: rgba(111, 45, 255, 0.9);
            box-shadow: 0 0 8px rgba(111, 45, 255, 0.6);
        }

        .section-title {
            padding: 1.5rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .add-btn {
            width: calc(100% - 3rem);
            margin: 0 1.5rem 1rem;
            padding: 0.6rem;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px dashed rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            color: white;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .add-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .item-container {
            background-color: rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            padding: 0.75rem;
            margin: 0 1.5rem 0.75rem;
            cursor: move;
            transition: all 0.2s;
            border: 2px solid transparent;
            position: relative;
        }

        .item-container:hover {
            background-color: rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .item-container.dragging {
            opacity: 0.3;
            transform: scale(0.95);
            border: 2px dashed rgba(255, 255, 255, 0.6);
        }

        .item-container.drag-over-top::before,
        .item-container.drag-over-bottom::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4CAF50, #66BB6A);
            box-shadow: 0 0 10px #4CAF50;
            animation: pulse 0.6s infinite;
            z-index: 10;
        }

        .item-container.drag-over-top::before {
            top: -3px;
        }

        .item-container.drag-over-bottom::after {
            bottom: -3px;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                box-shadow: 0 0 10px #4CAF50;
            }
            50% {
                opacity: 0.7;
                box-shadow: 0 0 20px #4CAF50;
            }
        }

        .item-container.drag-over-top,
        .item-container.drag-over-bottom {
            background-color: rgba(76, 175, 80, 0.15);
        }

        .item-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .drag-handle {
            width: 24px;
            height: 24px;
            cursor: move;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .drag-handle svg {
            width: 16px;
            height: 16px;
            stroke: rgba(255, 255, 255, 0.5);
        }

        .toggle-switch {
            width: 44px;
            height: 24px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s;
            flex-shrink: 0;
        }

        .toggle-switch.active {
            background-color: #4CAF50;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            background-color: white;
            border-radius: 50%;
            top: 3px;
            left: 3px;
            transition: transform 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .toggle-switch.active::after {
            transform: translateX(20px);
        }

        .input-short {
            width: 50px;
            padding: 6px 8px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
            flex-shrink: 0;
        }

        .input-long {
            flex: 1;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            min-width: 0;
        }

        .input-select {
            width: 60px;
            padding: 6px 8px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
            flex-shrink: 0;
            background-color: white;
            cursor: pointer;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 6px center;
            padding-right: 24px;
        }

        .input-select:hover {
            background-color: #f5f5f5;
        }

        .input-select:focus {
            box-shadow: 0 0 0 2px rgba(115, 49, 255, 0.3);
        }

        .delete-btn {
            width: 28px;
            height: 28px;
            background-color: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .delete-btn:hover {
            background-color: rgba(255, 100, 100, 0.6);
        }

        .delete-btn svg {
            width: 16px;
            height: 16px;
            stroke: #ff4444;
        }

        .delete-btn:hover svg {
            stroke: #ff0000;
        }

        .send-greeting-btn {
            width: 28px;
            height: 28px;
            background-color: rgba(76, 175, 80, 0.3);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .send-greeting-btn:hover {
            background-color: rgba(76, 175, 80, 0.6);
        }

        .send-greeting-btn svg {
            width: 16px;
            height: 16px;
            stroke: #4CAF50;
        }

        .send-greeting-btn:hover svg {
            stroke: #2E7D32;
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .save-btn {
            background-color: rgba(33, 150, 243, 0.3);
            color: white;
            border: 1px solid rgba(33, 150, 243, 0.5);
        }

        .save-btn:hover {
            background-color: rgba(33, 150, 243, 0.5);
        }

        .load-btn {
            background-color: rgba(255, 152, 0, 0.3);
            color: white;
            border: 1px solid rgba(255, 152, 0, 0.5);
        }

        .load-btn:hover {
            background-color: rgba(255, 152, 0, 0.5);
        }

        /* 모달 */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            animation: fadeIn 0.2s ease-in;
        }

        .modal-overlay.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.75rem;
            color: #999;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background-color: #f5f5f5;
            color: #333;
        }

        .modal-content {
            color: #333;
        }

        .help-section {
            margin-bottom: 2rem;
        }

        .help-section:last-child {
            margin-bottom: 0;
        }

        .help-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6f2dff;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .help-section-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #6f2dff 0%, #5a1fd9 100%);
            color: white;
            border-radius: 50%;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .help-section-content {
            padding-left: 0;
            line-height: 1.8;
            color: #555;
        }

        .help-section-content p {
            margin-bottom: 0.75rem;
        }

        .help-section-content strong {
            color: #333;
            font-weight: 600;
        }

        .help-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: linear-gradient(135deg, #6f2dff 0%, #5a1fd9 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 0.5rem;
            transition: all 0.2s;
        }

        .help-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(111, 45, 255, 0.3);
        }

        .code-block {
            background-color: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 1rem;
            margin: 0.75rem 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: #333;
            overflow-x: auto;
        }

        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }

        .warning-box-title {
            font-weight: 600;
            color: #856404;
            margin-bottom: 0.5rem;
        }

        .warning-box-content {
            color: #856404;
            font-size: 0.95rem;
        }

        .info-box {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }

        .info-box-title {
            font-weight: 600;
            color: #0c5460;
            margin-bottom: 0.5rem;
        }

        .info-box-content {
            color: #0c5460;
            font-size: 0.95rem;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* OAuth 계정 카드 스타일 */
        .oauth-account-card {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .oauth-account-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .oauth-account-card.selected {
            background: rgba(255, 255, 255, 0.2);
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.3);
        }

        .oauth-account-info {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .oauth-account-email {
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .oauth-account-divider {
            color: rgba(255, 255, 255, 0.4);
            font-weight: normal;
        }

        .oauth-account-channel {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .oauth-account-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 0.25rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* 반응형 */
        @media (max-width: 768px) {
            .sidebar {
                width: 70%;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .modal {
                max-width: 95%;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- 사이드바 -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div style="font-size: 20px;">ONETOP 프리미엄 채널관리</div>
        </div>

        <!-- 메뉴 영역 -->
        <ul class="menu-list">
            <li class="menu-item active" data-menu="subscriptionlist">📋 <a href="channel_manager.php">구독 리스트</a></li>
            <li class="menu-item" data-menu="scripttoimage">📋 시나리오 이미지 생성</li>
            <li class="menu-item" data-menu="idolprompt">📋 <a href="IDOL_Prompt_Generator_Simple_V4.php">IDOL 생성프롬프트</a></li>
        </ul>

        <!-- OAuth 2.0 인증 섹션 -->
        <div class="api-section">
            <div class="api-header">
                <h3 class="api-title">OAuth 2.0 인증 <button class="help-btn" onclick="openOAuthHelpModal()" title="OAuth 발급 방법">?</button></h3>
                <div class="api-status" id="oauthStatus">
                    <span class="status-dot inactive" id="oauthStatusDot"></span>
                    <span id="oauthStatusText">계정 연결 대기 중</span>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                <input
                    type="password"
                    class="api-input"
                    id="clientIdInput"
                    placeholder="클라이언트 ID를 입력하세요..."
                    autocomplete="off"
                    style="flex: 1;"
                >
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <input
                    type="password"
                    class="api-input"
                    id="clientSecretInput"
                    placeholder="클라이언트 보안 비밀번호를 입력하세요..."
                    autocomplete="off"
                    style="flex: 1;"
                >
            </div>

            <!-- OAuth 인증 버튼 -->
            <div style="margin-top: 0.75rem;">
                <button
                    class="oauth-connect-btn"
                    id="oauthConnectBtn"
                    onclick="connectGoogleAccount()"
                    style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(66, 133, 244, 0.4)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                >
                    🔐 Google 계정 연결
                </button>
            </div>
        </div>

        <!-- API 키 섹션 -->
        <div class="api-section">
            <div class="api-header">
                <h3 class="api-title">유튜브 API <button class="help-btn" onclick="openHelpModal()" title="API 발급 방법">?</button></h3>
                <div class="api-status" id="apiStatus">
                    <span class="status-dot inactive" id="statusDot"></span>
                    <span id="statusText">API 키가 입력되지 않았습니다</span>
                </div>
            </div>
            <div class="api-input-wrapper">
                <input
                    type="text"
                    class="api-input"
                    id="apiKeyInput"
                    placeholder="API 키를 입력하세요..."
                    autocomplete="off"
                >
            </div>
        </div>

        <!-- 채널 관리 섹션 -->
        <div class="api-section">
            <div class="api-header">
                <h3 class="api-title">연결된 채널</h3>
                <div style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.7);">
                    <span id="channelCount">0</span>개 채널
                </div>
            </div>

            <!-- 채널 목록 -->
            <div id="channelList" style="margin-bottom: 0.75rem;">
                <div id="noChannelsMessage" style="text-align: center; padding: 1.5rem; color: rgba(255, 255, 255, 0.6); font-size: 0.85rem;">
                    OAuth 인증 후 채널을 추가할 수 있습니다.
                </div>
            </div>

            <!-- 채널 관리 버튼 -->
            <div style="display: flex; gap: 0.5rem;">
                <button
                    id="addChannelBtn"
                    onclick="addNewChannel()"
                    disabled
                    style="flex: 1; padding: 0.75rem; background: rgba(76, 175, 80, 0.3); border: 1px solid rgba(76, 175, 80, 0.5); border-radius: 8px; color: white; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s; opacity: 0.5;"
                    onmouseover="if(!this.disabled) this.style.background='rgba(76, 175, 80, 0.5)';"
                    onmouseout="if(!this.disabled) this.style.background='rgba(76, 175, 80, 0.3)';"
                    title="채널 추가"
                >
                    ➕ 추가
                </button>
                <button
                    id="refreshTokenBtn"
                    onclick="manualRefreshToken()"
                    style="flex: 1; padding: 0.75rem; background: rgba(33, 150, 243, 0.3); border: 1px solid rgba(33, 150, 243, 0.5); border-radius: 8px; color: white; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.background='rgba(33, 150, 243, 0.5)';"
                    onmouseout="this.style.background='rgba(33, 150, 243, 0.3)';"
                    title="토큰 수동 갱신"
                >
                    🔄 토큰 갱신
                </button>
                <button
                    id="deleteChannelBtn"
                    onclick="deleteSelectedChannel()"
                    disabled
                    style="flex: 1; padding: 0.75rem; background: rgba(239, 68, 68, 0.3); border: 1px solid rgba(239, 68, 68, 0.5); border-radius: 8px; color: white; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s; opacity: 0.5; display: none;"
                    onmouseover="if(!this.disabled) this.style.background='rgba(239, 68, 68, 0.5)';"
                    onmouseout="if(!this.disabled) this.style.background='rgba(239, 68, 68, 0.3)';"
                    title="선택한 채널 삭제"
                >
                    🗑️ 삭제
                </button>
            </div>
        </div>
    </div>

    <!-- 도움말 모달 (생략 - 기존과 동일) -->
    <div class="modal-overlay" id="helpModal">
        <!-- ... 기존 내용 유지 ... -->
    </div>

    <!-- OAuth 도움말 모달 (생략 - 기존과 동일) -->
    <div class="modal-overlay" id="oauthHelpModal">
        <!-- ... 기존 내용 유지 ... -->
    </div>

    <script>
        // ==========================================
        // 전역 변수
        // ==========================================
        let apiKeyTimeout = null;
        let clientIdTimeout = null;
        let clientSecretTimeout = null;
        let isInitializing = true;
        let tokenRefreshInterval = null;

        // ==========================================
        // 🔥 핵심 토큰 갱신 함수
        // ==========================================
        async function refreshAccessToken(channelId) {
            console.log('🔄 [토큰 갱신] 시작 - 채널 ID:', channelId);

            const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');
            const channelIndex = channels.findIndex(ch => ch.id === channelId);

            if (channelIndex === -1) {
                console.error('❌ [토큰 갱신] 채널을 찾을 수 없음:', channelId);
                throw new Error('채널 정보를 찾을 수 없습니다.');
            }

            const channel = channels[channelIndex];
            const refreshToken = channel.refreshToken;
            const clientId = channel.clientId;
            const clientSecret = channel.clientSecret;

            console.log('📋 [토큰 갱신] 채널 정보:', {
                email: channel.email,
                channelName: channel.channelName,
                hasRefreshToken: !!refreshToken,
                hasClientId: !!clientId,
                hasClientSecret: !!clientSecret
            });

            if (!refreshToken) {
                console.error('❌ [토큰 갱신] Refresh Token 없음');
                throw new Error('Refresh Token이 없습니다. 채널을 삭제하고 다시 연결해주세요.');
            }

            if (!clientId || !clientSecret) {
                console.error('❌ [토큰 갱신] OAuth 인증 정보 없음');
                throw new Error('OAuth 인증 정보가 없습니다.');
            }

            try {
                const response = await fetch('https://oauth2.googleapis.com/token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        client_id: clientId,
                        client_secret: clientSecret,
                        refresh_token: refreshToken,
                        grant_type: 'refresh_token'
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('❌ [토큰 갱신] API 오류:', errorData);

                    if (errorData.error === 'invalid_grant') {
                        // ✅ 에러 플래그 설정
                        channels[channelIndex].tokenError = true;
                        localStorage.setItem('connectedChannels', JSON.stringify(channels));
                        renderChannelList();

                        throw new Error('인증이 만료되었습니다. 채널을 삭제하고 다시 연결해주세요.');
                    }

                    throw new Error(errorData.error_description || 'Token 갱신 실패');
                }

                const data = await response.json();

                // ✅ 새 토큰 정보 저장 (expiresAt 반드시 저장!)
                const newAccessToken = data.access_token;
                const expiresIn = data.expires_in || 3600; // 기본값 1시간
                const expiresAt = new Date().getTime() + (expiresIn * 1000);

                console.log('✅ [토큰 갱신] 성공:', {
                    expiresIn: expiresIn + '초',
                    expiresAt: new Date(expiresAt).toLocaleString('ko-KR')
                });

                channels[channelIndex].accessToken = newAccessToken;
                channels[channelIndex].expiresAt = expiresAt;

                // Refresh Token이 새로 발급된 경우 업데이트
                if (data.refresh_token) {
                    console.log('🔄 [토큰 갱신] 새 Refresh Token 발급됨');
                    channels[channelIndex].refreshToken = data.refresh_token;
                }

                // ✅ 에러 플래그 제거
                delete channels[channelIndex].tokenError;

                localStorage.setItem('connectedChannels', JSON.stringify(channels));

                // UI 업데이트
                renderChannelList();

                return newAccessToken;

            } catch (error) {
                console.error('❌ [토큰 갱신] 실패:', error);
                throw error;
            }
        }

        // ==========================================
        // 🔥 토큰 유효성 검사 및 자동 갱신
        // ==========================================
        async function getValidAccessToken(channelId) {
            console.log('🔍 [토큰 검증] 시작 - 채널 ID:', channelId);

            const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');
            const channel = channels.find(ch => ch.id === channelId);

            if (!channel) {
                console.error('❌ [토큰 검증] 채널을 찾을 수 없음');
                throw new Error('채널 정보를 찾을 수 없습니다.');
            }

            const accessToken = channel.accessToken;
            const expiresAt = channel.expiresAt;
            const now = new Date().getTime();

            // ✅ 상세 로그
            console.log('📊 [토큰 검증] 토큰 상태:', {
                hasToken: !!accessToken,
                expiresAt: expiresAt ? new Date(expiresAt).toLocaleString('ko-KR') : '없음',
                now: new Date(now).toLocaleString('ko-KR'),
                remainingMinutes: expiresAt ? Math.floor((expiresAt - now) / 60000) : '없음',
                isExpired: !expiresAt || now >= expiresAt,
                needsRefresh: !expiresAt || now >= (expiresAt - 10 * 60 * 1000)
            });

            // ✅ 토큰이 없거나, 만료 시간이 없거나, 10분 이내 만료 예정이면 갱신
            if (!accessToken || !expiresAt || now >= (expiresAt - 10 * 60 * 1000)) {
                console.log('🔄 [토큰 검증] 갱신 필요 - 갱신 시작');
                return await refreshAccessToken(channelId);
            }

            console.log('✅ [토큰 검증] 유효함 - 갱신 불필요');
            return accessToken;
        }

        // ==========================================
        // 🔥 YouTube API 호출 헬퍼 함수 (자동 토큰 갱신 포함)
        // ==========================================
        async function makeYouTubeAPICall(url, channelId, options = {}) {
            console.log('📡 [API 호출] 시작:', url);

            try {
                // ✅ 1. 유효한 토큰 가져오기 (자동 갱신)
                const accessToken = await getValidAccessToken(channelId);

                // ✅ 2. API 호출
                const response = await fetch(url, {
                    ...options,
                    headers: {
                        ...options.headers,
                        'Authorization': `Bearer ${accessToken}`,
                        'Accept': 'application/json'
                    }
                });

                // ✅ 3. 401 에러 시 재시도
                if (response.status === 401) {
                    console.warn('⚠️ [API 호출] 401 에러 - 토큰 갱신 후 재시도');

                    // 토큰 강제 갱신
                    const newAccessToken = await refreshAccessToken(channelId);

                    // 재시도
                    const retryResponse = await fetch(url, {
                        ...options,
                        headers: {
                            ...options.headers,
                            'Authorization': `Bearer ${newAccessToken}`,
                            'Accept': 'application/json'
                        }
                    });

                    console.log('✅ [API 호출] 재시도 완료:', retryResponse.status);
                    return retryResponse;
                }

                console.log('✅ [API 호출] 성공:', response.status);
                return response;

            } catch (error) {
                console.error('❌ [API 호출] 실패:', error);
                throw error;
            }
        }

        // ==========================================
        // 🔥 페이지 로드 시 자동 토큰 검증 및 갱신
        // ==========================================
        async function validateAndRestoreChannels() {
            console.log('🔍 [자동 검증] 시작...');

            const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');

            if (channels.length === 0) {
                console.log('⚠️ [자동 검증] 연결된 채널 없음');
                return;
            }

            console.log(`📋 [자동 검증] ${channels.length}개 채널 검증 시작`);

            for (const channel of channels) {
                try {
                    console.log(`🔄 [자동 검증] 채널 "${channel.email}" 검증 중...`);

                    // ✅ 토큰 검증 및 필요 시 갱신
                    await getValidAccessToken(channel.id);

                    console.log(`✅ [자동 검증] 채널 "${channel.email}" 검증 완료`);

                } catch (error) {
                    console.error(`❌ [자동 검증] 채널 "${channel.email}" 실패:`, error);
                }
            }

            console.log('✅ [자동 검증] 완료');
        }

        // ==========================================
        // 🔥 주기적 토큰 갱신 (10분마다 체크)
        // ==========================================
        function startTokenRefreshInterval() {
            // 기존 인터벌 제거
            if (tokenRefreshInterval) {
                clearInterval(tokenRefreshInterval);
            }

            // 10분마다 토큰 상태 체크
            tokenRefreshInterval = setInterval(async function() {
                console.log('⏰ [주기적 체크] 토큰 상태 확인 시작...');

                const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');
                const now = new Date().getTime();

                for (const channel of channels) {
                    // 만료 시간이 15분 이내면 갱신
                    if (channel.expiresAt && now >= (channel.expiresAt - 15 * 60 * 1000)) {
                        console.log(`🔄 [주기적 체크] 채널 "${channel.email}" 갱신 필요`);

                        try {
                            await refreshAccessToken(channel.id);
                            console.log(`✅ [주기적 체크] 채널 "${channel.email}" 갱신 완료`);
                        } catch (error) {
                            console.error(`❌ [주기적 체크] 채널 "${channel.email}" 갱신 실패:`, error);
                        }
                    }
                }
            }, 10 * 60 * 1000); // 10분마다

            console.log('✅ [주기적 체크] 인터벌 시작 (10분마다)');
        }

        // ==========================================
        // 🔥 채널 선택 시 자동 토큰 검증 (개선)
        // ==========================================
        async function selectChannel(channelId) {
            console.log('🎯 [채널 선택] 시작 - 채널 ID:', channelId);

            const previousChannelId = localStorage.getItem('currentChannelId');

            // ✅ 1. 채널 ID 저장
            localStorage.setItem('currentChannelId', channelId);

            // ✅ 2. UI 업데이트
            renderChannelList();

            // ✅ 3. 채널이 변경된 경우 토큰 검증 및 데이터 로드
            if (previousChannelId !== channelId) {
                try {
                    console.log('🔄 [채널 선택] 토큰 검증 시작...');

                    // ✅ 토큰 검증 및 필요 시 자동 갱신
                    await getValidAccessToken(channelId);

                    console.log('✅ [채널 선택] 토큰 검증 완료');

                    // ✅ 4. 채널 변경 이벤트 발생
                    const timestamp = Date.now().toString();
                    localStorage.setItem('channelChangedTimestamp', timestamp);

                    window.dispatchEvent(new CustomEvent('channelChanged', {
                        detail: {
                            channelId: channelId,
                            previousChannelId: previousChannelId,
                            timestamp: timestamp
                        }
                    }));

                    // ✅ 5. 구독 리스트 로드
                    if (typeof window.loadSubscriptions === 'function') {
                        console.log('📥 [채널 선택] 구독 리스트 로드 시작...');
                        window.loadSubscriptions();
                    }

                } catch (error) {
                    console.error('❌ [채널 선택] 토큰 검증 실패:', error);
                    alert('⚠️ 토큰 검증 실패\n\n' + error.message);
                }
            } else {
                console.log('ℹ️ [채널 선택] 동일한 채널 - 토큰 검증 생략');
            }
        }

        // ==========================================
        // 수동 토큰 갱신
        // ==========================================
        async function manualRefreshToken() {
            console.log('🔧 [수동 갱신] 시작...');

            const currentId = localStorage.getItem('currentChannelId');

            if (!currentId) {
                alert('⚠️ 선택된 채널이 없습니다.');
                return;
            }

            try {
                const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');
                const channel = channels.find(ch => ch.id === currentId);

                if (!channel) {
                    alert('❌ 채널 정보를 찾을 수 없습니다.');
                    return;
                }

                alert(`🔄 "${channel.email}" 토큰 갱신을 시작합니다...`);

                const newToken = await refreshAccessToken(currentId);

                if (newToken) {
                    alert('✅ 토큰 갱신 성공!\n\n이제 채널을 다시 사용할 수 있습니다.');

                    // 구독 리스트 자동 로드
                    if (typeof window.loadSubscriptions === 'function') {
                        window.loadSubscriptions();
                    }
                }

            } catch (error) {
                console.error('❌ [수동 갱신] 실패:', error);
                alert('❌ 토큰 갱신 실패\n\n' + error.message + '\n\n채널을 삭제하고 다시 추가해주세요.');
            }
        }

        // ==========================================
        // 기타 기존 함수들
        // ==========================================

        function connectGoogleAccount() {
            console.log('🔐 Google 계정 연결 시작...');

            const clientId = document.getElementById('clientIdInput').value.trim();
            const clientSecret = document.getElementById('clientSecretInput').value.trim();

            if (!clientId || !clientSecret) {
                alert('⚠️ 클라이언트 ID와 보안 비밀번호를 입력하세요.');
                return;
            }

            localStorage.setItem('oauthClientId', clientId);
            localStorage.setItem('oauthClientSecret', clientSecret);

            startOAuthFlow();
        }

        function startOAuthFlow() {
            const clientId = document.getElementById('clientIdInput').value.trim();
            const clientSecret = document.getElementById('clientSecretInput').value.trim();

            localStorage.setItem('tempClientId', clientId);
            localStorage.setItem('tempClientSecret', clientSecret);

            const redirectUri = 'http://showrank.kr/premium_ch/oauth2callback.php';
            const scope = 'https://www.googleapis.com/auth/youtube.force-ssl https://www.googleapis.com/auth/userinfo.email';
            const state = Math.random().toString(36).substring(2, 15);

            localStorage.setItem('oauthState', state);

            const authUrl = new URL('https://accounts.google.com/o/oauth2/v2/auth');
            authUrl.searchParams.append('client_id', clientId);
            authUrl.searchParams.append('redirect_uri', redirectUri);
            authUrl.searchParams.append('response_type', 'code');
            authUrl.searchParams.append('scope', scope);
            authUrl.searchParams.append('access_type', 'offline');
            authUrl.searchParams.append('prompt', 'consent');
            authUrl.searchParams.append('state', state);

            window.location.href = authUrl.toString();
        }

        function checkOAuthConnection() {
            const accessToken = localStorage.getItem('oauthAccessToken');
            const statusDot = document.getElementById('oauthStatusDot');
            const statusText = document.getElementById('oauthStatusText');
            const connectBtn = document.getElementById('oauthConnectBtn');
            const addChannelBtn = document.getElementById('addChannelBtn');

            if (accessToken) {
                statusDot.classList.remove('inactive');
                statusText.textContent = 'OAuth 인증 완료';
                connectBtn.textContent = '✅ 연결됨';
                connectBtn.style.background = 'linear-gradient(135deg, #34a853 0%, #0f9d58 100%)';

                if (addChannelBtn) {
                    addChannelBtn.disabled = false;
                    addChannelBtn.style.opacity = '1';
                    addChannelBtn.style.cursor = 'pointer';
                }
            } else {
                statusDot.classList.add('inactive');
                statusText.textContent = '계정 연결 대기 중';
                connectBtn.textContent = '🔐 Google 계정 연결';
                connectBtn.style.background = 'linear-gradient(135deg, #4285f4 0%, #34a853 100%)';

                if (addChannelBtn) {
                    addChannelBtn.disabled = true;
                    addChannelBtn.style.opacity = '0.5';
                    addChannelBtn.style.cursor = 'not-allowed';
                }
            }
        }

        function renderChannelList() {
            const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');
            const currentId = localStorage.getItem('currentChannelId');
            const listContainer = document.getElementById('channelList');
            const channelCount = document.getElementById('channelCount');

            if (!listContainer) return;

            if (channelCount) channelCount.textContent = channels.length;

            if (channels.length === 0) {
                listContainer.innerHTML = `
                    <div id="noChannelsMessage" style="text-align: center; padding: 1.5rem; color: rgba(255, 255, 255, 0.6); font-size: 0.85rem;">
                        OAuth 인증 후 채널을 추가할 수 있습니다.
                    </div>
                `;
                updateDeleteChannelButton();
                return;
            }

            let cardsHTML = '';
            channels.forEach(channel => {
                const isSelected = channel.id === currentId;
                const selectedClass = isSelected ? 'selected' : '';
                const displayEmail = channel.email || '⚠️ 이메일 정보 없음';
                const displayChannelName = channel.channelName || '⚠️ 채널 정보 없음';

                const errorBadge = channel.tokenError ?
                    '<span class="oauth-account-badge" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">⚠️ 인증 만료</span>' :
                    '';

                cardsHTML += `
                    <div class="oauth-account-card ${selectedClass}"
                        onclick="selectChannel('${channel.id}')"
                        data-channel-id="${channel.id}"
                        ${channel.tokenError ? 'style="border-color: #ef4444;"' : ''}>
                        <div class="oauth-account-info">
                            <span class="oauth-account-email">📧 ${displayEmail}</span>
                            <span class="oauth-account-divider">|</span>
                            <span class="oauth-account-channel">📺 ${displayChannelName}</span>
                        </div>
                        ${errorBadge}
                        ${isSelected && !channel.tokenError ? '<span class="oauth-account-badge">✓ 선택됨</span>' : ''}
                    </div>
                `;
            });

            listContainer.innerHTML = cardsHTML;
            updateDeleteChannelButton();
        }

        function addNewChannel() {
            const accessToken = localStorage.getItem('oauthAccessToken');

            if (!accessToken) {
                alert('⚠️ 먼저 Google 계정 연결을 완료해주세요.');
                return;
            }

            localStorage.setItem('oauthMode', 'addChannel');

            const clientId = localStorage.getItem('oauthClientId');
            const clientSecret = localStorage.getItem('oauthClientSecret');

            if (!clientId || !clientSecret) {
                alert('⚠️ Client ID/Secret이 없습니다.');
                return;
            }

            localStorage.setItem('tempClientId', clientId);
            localStorage.setItem('tempClientSecret', clientSecret);

            const currentUrl = window.location.href;
            const baseUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/') + 1);
            const redirectUri = baseUrl + 'oauth2callback.php';
            const scope = 'https://www.googleapis.com/auth/youtube.force-ssl https://www.googleapis.com/auth/userinfo.email';
            const state = Math.random().toString(36).substring(2, 15);

            localStorage.setItem('oauthState', state);

            const authUrl = new URL('https://accounts.google.com/o/oauth2/v2/auth');
            authUrl.searchParams.append('client_id', clientId);
            authUrl.searchParams.append('redirect_uri', redirectUri);
            authUrl.searchParams.append('response_type', 'code');
            authUrl.searchParams.append('scope', scope);
            authUrl.searchParams.append('access_type', 'offline');
            authUrl.searchParams.append('prompt', 'select_account');
            authUrl.searchParams.append('state', state);

            window.location.href = authUrl.toString();
        }

        function deleteSelectedChannel() {
            const currentId = localStorage.getItem('currentChannelId');
            if (!currentId) {
                alert('선택된 채널이 없습니다.');
                return;
            }

            const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');
            const channel = channels.find(ch => ch.id === currentId);

            if (!channel || !confirm(`"${channel.email}" 채널을 삭제하시겠습니까?`)) {
                return;
            }

            const updatedChannels = channels.filter(ch => ch.id !== currentId);
            localStorage.setItem('connectedChannels', JSON.stringify(updatedChannels));
            localStorage.removeItem('currentChannelId');

            renderChannelList();
            alert('채널이 삭제되었습니다.');
        }

        function updateDeleteChannelButton() {
            const currentId = localStorage.getItem('currentChannelId');
            const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');
            const deleteBtn = document.getElementById('deleteChannelBtn');

            if (deleteBtn) {
                if (currentId && channels.length > 0 && channels.find(ch => ch.id === currentId)) {
                    deleteBtn.style.display = 'block';
                    deleteBtn.disabled = false;
                    deleteBtn.style.opacity = '1';
                } else {
                    deleteBtn.style.display = 'none';
                }
            }
        }

        function loadApiKey() {
            const savedKey = localStorage.getItem('youtubeApiKey');
            if (savedKey) {
                document.getElementById('apiKeyInput').value = savedKey;
                updateApiStatus(true);
            }
        }

        function updateApiStatus(isActive) {
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');

            if (isActive) {
                statusDot.classList.remove('inactive');
                statusText.textContent = 'API 키가 활성화되었습니다';
            } else {
                statusDot.classList.add('inactive');
                statusText.textContent = 'API 키가 입력되지 않았습니다';
            }
        }

        function openHelpModal() {
            document.getElementById('helpModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeHelpModal() {
            document.getElementById('helpModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function openOAuthHelpModal() {
            document.getElementById('oauthHelpModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeOAuthHelpModal() {
            document.getElementById('oauthHelpModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // ==========================================
        // 🔥 첫 채널 자동 선택
        // ==========================================
        function autoSelectFirstChannel() {
            const currentId = localStorage.getItem('currentChannelId');
            const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');

            // ✅ 선택된 채널이 없고, 연결된 채널이 있으면 첫 번째 채널 자동 선택
            if (!currentId && channels.length > 0) {
                console.log('🎯 [자동 선택] 첫 번째 채널 자동 선택:', channels[0].email);
                selectChannel(channels[0].id);
            }
        }

        // ==========================================
        // 🔥 페이지 로드 시 초기화
        // ==========================================
        window.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 [초기화] 시작');

            // 기본 설정 로드
            loadApiKey();

            // OAuth Client ID/Secret 자동 입력
            const savedClientId = localStorage.getItem('oauthClientId');
            const savedClientSecret = localStorage.getItem('oauthClientSecret');

            if (savedClientId) {
                document.getElementById('clientIdInput').value = savedClientId;
            }
            if (savedClientSecret) {
                document.getElementById('clientSecretInput').value = savedClientSecret;
            }

            // OAuth 및 채널 초기화
            renderChannelList();
            checkOAuthConnection();

            // ✅ 2초 후 초기화 완료
            setTimeout(function() {
                isInitializing = false;
                console.log('✅ [초기화] 보호 해제');
            }, 2000);

            // API 키 입력 이벤트
            document.getElementById('apiKeyInput').addEventListener('input', function(e) {
                const apiKey = e.target.value.trim();

                if (apiKeyTimeout) clearTimeout(apiKeyTimeout);

                apiKeyTimeout = setTimeout(() => {
                    if (apiKey) {
                        localStorage.setItem('youtubeApiKey', apiKey);
                        updateApiStatus(true);
                    } else {
                        localStorage.removeItem('youtubeApiKey');
                        updateApiStatus(false);
                    }
                }, 500);
            });

            // OAuth 입력 필드 이벤트
            document.getElementById('clientIdInput').addEventListener('input', function(e) {
                if (isInitializing) return;

                const clientId = e.target.value.trim();

                if (clientIdTimeout) clearTimeout(clientIdTimeout);

                clientIdTimeout = setTimeout(() => {
                    if (clientId) {
                        localStorage.setItem('oauthClientId', clientId);
                    }
                }, 500);
            });

            document.getElementById('clientSecretInput').addEventListener('input', function(e) {
                if (isInitializing) return;

                const clientSecret = e.target.value.trim();

                if (clientSecretTimeout) clearTimeout(clientSecretTimeout);

                clientSecretTimeout = setTimeout(() => {
                    if (clientSecret) {
                        localStorage.setItem('oauthClientSecret', clientSecret);
                    }
                }, 500);
            });

            // ✅ 즉시 자동 토큰 검증 실행 (백그라운드)
            setTimeout(async function() {
                console.log('🔍 [자동 검증] 즉시 실행');
                await validateAndRestoreChannels();

                // ✅ 주기적 토큰 갱신 시작
                startTokenRefreshInterval();

                // ✅ 첫 채널 자동 선택
                autoSelectFirstChannel();
            }, 500); // 0.5초 후 실행

            console.log('✅ [초기화] 완료');
        });

        // ✅ 전역 함수 등록 (다른 페이지에서 접근 가능)
        window.refreshAccessToken = refreshAccessToken;
        window.getValidAccessToken = getValidAccessToken;
        window.makeYouTubeAPICall = makeYouTubeAPICall;
    </script>
</body>
</html>
