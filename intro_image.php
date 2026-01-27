<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ONETOP - 인트로 이미지 생성</title>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="shortcut icon" href="img/favicon.ico">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .main-content {
            margin-left: 600px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f5f7fa;
        }

        .page-header {
            background: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }

        .content-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }

        .section-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            max-width: 1000px;
            margin: 0 auto;
        }

        .section-card-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px 16px 0 0;
            color: white;
        }

        .section-card-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .section-card-header p {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        .section-card-body {
            padding: 2rem;
        }

        /* API 키 입력 영역 */
        .api-key-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .api-key-section label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .api-key-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .api-key-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }

        /* 안내 박스 */
        .guide-box {
            background: #f0f4ff;
            border: 1px solid #d0d9ff;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.88rem;
            line-height: 1.8;
            color: #444;
        }

        .guide-box strong {
            color: #333;
        }

        /* 이미지 생성 영역 */
        .image-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .image-column {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        .image-preview {
            width: 100%;
            aspect-ratio: 1;
            border: 2px dashed #d1d5db;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
            overflow: hidden;
            transition: border-color 0.3s;
        }

        .image-preview:hover {
            border-color: #667eea;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 14px;
        }

        .image-preview .placeholder {
            color: #9ca3af;
            font-size: 0.9rem;
            text-align: center;
        }

        /* 채널 정보 표시 */
        .channel-info-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .channel-info-item {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .channel-info-item span {
            font-weight: 600;
            color: #667eea;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .image-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- 사이드바 Include -->
    <?php include 'z_menu.php'; ?>

    <!-- 메인 컨텐츠 -->
    <div class="main-content">
        <div class="page-header">
            <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
            <h1 class="page-title" style="flex: 1; text-align: center;">인트로 이미지 생성</h1>
            <div style="width: 60px;"></div>
        </div>

        <div class="content-wrapper">
            <div class="section-card">
                <div class="section-card-header">
                    <h3>인트로 이미지 생성</h3>
                    <p>인트로 영상용 이미지를 생성합니다.</p>
                </div>

                <div class="section-card-body">
                    <!-- Google AI Studio API 키 -->
                    <div class="api-key-section">
                        <label>Google AI Studio API Key</label>
                        <input type="password" class="api-key-input" id="googleApiKeyInput"
                            placeholder="Google AI Studio API 키를 입력하세요..." autocomplete="off">
                    </div>

                    <!-- 채널 정보 -->
                    <div class="channel-info-bar" id="channelInfoBar" style="display: none;">
                        <div class="channel-info-item">채널명: <span id="displayChannelName">-</span></div>
                        <div class="channel-info-item">핸들: <span id="displayHandleName">-</span></div>
                    </div>

                    <!-- 안내 박스 -->
                    <div class="guide-box">
                        <strong>1. 이미지 생성 → Flow에서 영상 제작</strong><br>
                        &nbsp;&nbsp;생성된 이미지 + 첨부된 이미지를 Flow에서 영상으로 만드세요.<br>
                        &nbsp;&nbsp;- 생성된 이미지, 첨부된 이미지 모두 다운로드<br>
                        &nbsp;&nbsp;- <a href="https://labs.google/fx/ko" target="_blank" style="color: #667eea;">https://labs.google/fx/ko</a> 에 접속<br>
                        &nbsp;&nbsp;- 에셋으로 동영상 만들기<br>
                        &nbsp;&nbsp;- 생성 이미지 추가 후 구독 이미지 추가<br>
                        &nbsp;&nbsp;- effect 단어만 입력 후 실행
                    </div>

                    <!-- 이미지 생성 영역 -->
                    <div class="image-grid">
                        <!-- 인트로 이미지 -->
                        <div class="image-column">
                            <button class="btn btn-primary" type="button" id="generateIntroBtn" onclick="apicall_image('intro')">
                                인트로 이미지 생성
                            </button>
                            <div class="image-preview" id="introPreview">
                                <span class="placeholder">인트로 이미지 미리보기</span>
                            </div>
                            <button id="introDownloadBtn" class="btn btn-secondary" onclick="downloadImage('intro')" style="display: none;">
                                인트로 이미지 다운로드
                            </button>
                        </div>

                        <!-- 구독 아이콘 -->
                        <div class="image-column">
                            <button class="btn btn-primary" type="button" disabled style="opacity: 0.5;">
                                구독 아이콘
                            </button>
                            <div class="image-preview" id="subscribePreview">
                                <img src="img/icons/subscribe.png"
                                    onerror="this.parentElement.innerHTML='<span class=\'placeholder\'>구독 아이콘<br>(img/icons/subscribe.png)</span>';">
                            </div>
                            <button id="subscribeDownloadBtn" class="btn btn-secondary" onclick="downloadImage('subscribe')" style="display: none;">
                                구독 아이콘 다운로드
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==========================================
        // 초기화
        // ==========================================
        window.addEventListener('DOMContentLoaded', function() {
            // Google AI Studio API 키 복원
            const savedKey = localStorage.getItem('googleAIStudioApiKey');
            if (savedKey) {
                document.getElementById('googleApiKeyInput').value = savedKey;
            }

            // API 키 입력 시 자동 저장
            document.getElementById('googleApiKeyInput').addEventListener('input', function(e) {
                const key = e.target.value.trim();
                if (key) {
                    localStorage.setItem('googleAIStudioApiKey', key);
                } else {
                    localStorage.removeItem('googleAIStudioApiKey');
                }
            });

            // 채널 정보 표시
            loadChannelInfo();

            // 구독 아이콘 이미지가 존재하면 다운로드 버튼 표시
            const subscribeImg = document.querySelector('#subscribePreview img');
            if (subscribeImg) {
                subscribeImg.addEventListener('load', function() {
                    document.getElementById('subscribeDownloadBtn').style.display = 'block';
                });
            }
        });

        // ==========================================
        // 채널 정보 표시
        // ==========================================
        function loadChannelInfo() {
            const channelName = localStorage.getItem('selectedChannelName');
            const handleName = localStorage.getItem('selectedHandleName');

            if (channelName || handleName) {
                document.getElementById('channelInfoBar').style.display = 'flex';
                document.getElementById('displayChannelName').textContent = channelName || '-';
                document.getElementById('displayHandleName').textContent = handleName ? ('@' + handleName) : '-';
            }
        }

        // ==========================================
        // 이미지 생성 API 호출
        // ==========================================
        function apicall_image(type) {
            const apiKey = document.getElementById('googleApiKeyInput').value.trim() ||
                           localStorage.getItem('googleAIStudioApiKey');

            if (!apiKey) {
                alert('Google AI Studio API 키를 입력해주세요.');
                document.getElementById('googleApiKeyInput').focus();
                return;
            }

            // API 키 저장
            localStorage.setItem('googleAIStudioApiKey', apiKey);

            const btn = document.getElementById('generateIntroBtn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = '생성 중...';

            const previewId = type === 'intro' ? 'introPreview' : 'subscribePreview';
            const preview = document.getElementById(previewId);
            preview.innerHTML = '<span class="placeholder" style="color: #6366f1;">이미지 생성 중...</span>';

            // localStorage에서 채널 정보 가져오기
            let keywords = [];
            try {
                keywords = JSON.parse(localStorage.getItem('selectedKeywords') || '[]');
            } catch(e) {}

            const payload = {
                apiKey: apiKey,
                type: type,
                channelName: localStorage.getItem('selectedChannelName') || '',
                handleName: localStorage.getItem('selectedHandleName') || '',
                description: localStorage.getItem('selectedChannelDescription') || '',
                keywords: keywords.join(', ')
            };

            fetch('/api/gemini_intro_image.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(function(res) {
                return res.text().then(function(text) {
                    console.log('Image API response:', res.status, text.substring(0, 200));
                    if (!text) throw new Error('서버 응답이 비어있습니다 (HTTP ' + res.status + ')');
                    try {
                        return JSON.parse(text);
                    } catch(e) {
                        throw new Error('JSON 파싱 실패: ' + text.substring(0, 200));
                    }
                });
            })
            .then(function(data) {
                if (!data.ok) throw new Error(data.error || 'API 오류');

                var img = document.createElement('img');
                img.src = 'data:' + (data.mimeType || 'image/png') + ';base64,' + data.image;
                preview.innerHTML = '';
                preview.appendChild(img);

                // base64 데이터 저장
                localStorage.setItem('introImageBase64', data.image);
                localStorage.setItem('introImageMime', data.mimeType || 'image/png');

                // 다운로드 버튼 표시
                document.getElementById('introDownloadBtn').style.display = 'block';
            })
            .catch(function(err) {
                console.error('Image generation error:', err);
                preview.innerHTML = '<span class="placeholder" style="color: #ef4444; font-size: 0.85rem;">생성 실패: ' + err.message + '</span>';
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = originalText;
            });
        }

        // ==========================================
        // 이미지 다운로드
        // ==========================================
        function downloadImage(type) {
            var base64, mimeType, filename;

            if (type === 'intro') {
                base64 = localStorage.getItem('introImageBase64');
                mimeType = localStorage.getItem('introImageMime') || 'image/png';
                filename = 'intro_image.' + (mimeType.split('/')[1] || 'png');
            } else if (type === 'subscribe') {
                // 구독 아이콘은 이미지 요소에서 직접 다운로드
                var img = document.querySelector('#subscribePreview img');
                if (img && img.src) {
                    var a = document.createElement('a');
                    a.href = img.src;
                    a.download = 'subscribe_icon.png';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    return;
                }
                alert('구독 아이콘 이미지가 없습니다.');
                return;
            }

            if (!base64) {
                alert('먼저 이미지를 생성해주세요.');
                return;
            }

            // base64를 Blob으로 변환 후 다운로드
            var byteCharacters = atob(base64);
            var byteNumbers = new Array(byteCharacters.length);
            for (var i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            var byteArray = new Uint8Array(byteNumbers);
            var blob = new Blob([byteArray], { type: mimeType });

            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // ==========================================
        // 사이드바 토글
        // ==========================================
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
