<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI 자동화 생성</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
        }

        /* 섹션 카드 */
        .section-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin: 2rem;
        }

        .section-card-body {
            padding: 2rem;
        }

        /* 2:1 레이아웃 컨테이너 */
        .split-layout {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        .split-layout-left {
            flex: 2;
        }

        .split-layout-right {
            flex: 1;
        }

        /* 입력 폼 */
        .form-group {
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        /* 옵션 그리드 */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
        }

        .option-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.25rem 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .option-card:hover {
            border-color: #6f2dff;
            background: #f3f0ff;
            transform: translateY(-2px);
        }

        .option-card.selected {
            border-color: #6f2dff;
            background: linear-gradient(135deg, #f3f0ff 0%, #ebe4ff 100%);
            box-shadow: 0 4px 12px rgba(111, 45, 255, 0.2);
        }

        .option-icon {
            margin-bottom: 0.75rem;
        }

        .icon-svg {
            width: 32px;
            height: 32px;
            opacity: 0.7;
        }

        .option-card:hover .icon-svg,
        .option-card.selected .icon-svg {
            opacity: 1;
        }

        .option-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .option-description {
            font-size: 0.8rem;
            color: #666;
        }

        /* 등장인물 이미지 업로드 */
        .character-upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
        }

        .character-upload-box {
            width: 100%;
            aspect-ratio: 1 / 1;
            max-width: 200px;
            border: 2px dashed #555;
            border-radius: 12px;
            background: #1a1a2e;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .character-upload-box:hover {
            border-color: #6f2dff;
            background: #252540;
        }

        .character-upload-box.has-image {
            border-style: solid;
            border-color: #6f2dff;
        }

        .character-upload-icon {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: #888;
        }

        .character-upload-icon svg {
            width: 48px;
            height: 48px;
            stroke: #888;
        }

        .character-upload-box:hover .character-upload-icon svg {
            stroke: #6f2dff;
        }

        .character-upload-box:hover .character-upload-icon {
            color: #6f2dff;
        }

        .upload-plus {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #888;
            font-weight: 300;
        }

        .character-upload-box:hover .upload-plus {
            background: rgba(111, 45, 255, 0.2);
            color: #6f2dff;
        }

        .character-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .character-upload-box.has-image .character-preview {
            display: block;
        }

        .character-upload-box.has-image .character-upload-icon,
        .character-upload-box.has-image .upload-plus {
            display: none;
        }

        .character-upload-box.has-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0);
            transition: background 0.3s;
        }

        .character-upload-box.has-image:hover::after {
            background: rgba(0, 0, 0, 0.5);
        }

        .character-upload-box.has-image:hover::before {
            content: '변경';
            position: absolute;
            z-index: 1;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .remove-image-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            background: rgba(239, 68, 68, 0.9);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 16px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: all 0.2s;
        }

        .character-upload-box.has-image:hover .remove-image-btn {
            display: flex;
        }

        .remove-image-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* 숨겨진 파일 입력 */
        .hidden-input {
            display: none;
        }

        /* 반응형 */
        @media (max-width: 768px) {
            .split-layout {
                flex-direction: column;
            }

            .split-layout-left,
            .split-layout-right {
                flex: none;
                width: 100%;
            }

            .options-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .character-upload-box {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>

    <!-- 메인 컨텐츠 -->
    <div class="section-card">
        <div class="section-card-body">
            <div class="split-layout">
                <!-- 좌측 2/3: 영상 비율 선택 -->
                <div class="split-layout-left">
                    <div class="form-group">
                        <label class="form-label">영상 비율 선택</label>
                        <div class="options-grid" id="ratioOptions">
                            <div class="option-card" data-value="16:9" onclick="selectRatio(this)">
                                <div class="option-icon">
                                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                        <line x1="8" y1="21" x2="16" y2="21"></line>
                                        <line x1="12" y1="17" x2="12" y2="21"></line>
                                    </svg>
                                </div>
                                <div class="option-title">16:9</div>
                                <div class="option-description">유튜브, 데스크톱</div>
                            </div>
                            <div class="option-card" data-value="1:1" onclick="selectRatio(this)">
                                <div class="option-icon">
                                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    </svg>
                                </div>
                                <div class="option-title">1:1</div>
                                <div class="option-description">인스타그램, 소셜</div>
                            </div>
                            <div class="option-card" data-value="9:16" onclick="selectRatio(this)">
                                <div class="option-icon">
                                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                    </svg>
                                </div>
                                <div class="option-title">9:16</div>
                                <div class="option-description">틱톡, 릴스, 쇼츠</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 우측 1/3: 등장인물 이미지 추가 -->
                <div class="split-layout-right">
                    <div class="form-group">
                        <label class="form-label">등장인물 이미지 추가</label>
                        <div class="character-upload-container">
                            <div class="character-upload-box" id="characterUploadBox" onclick="document.getElementById('characterImageInput').click()">
                                <span class="upload-plus">+</span>
                                <div class="character-upload-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <img class="character-preview" id="characterPreview" src="" alt="캐릭터 미리보기">
                                <button type="button" class="remove-image-btn" id="removeImageBtn" onclick="removeCharacterImage(event)">&times;</button>
                            </div>
                            <input type="file" id="characterImageInput" class="hidden-input" accept="image/*" onchange="handleCharacterImageUpload(event)">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 영상 비율 선택
        function selectRatio(element) {
            // 기존 선택 해제
            document.querySelectorAll('#ratioOptions .option-card').forEach(card => {
                card.classList.remove('selected');
            });

            // 새로운 선택
            element.classList.add('selected');

            const selectedValue = element.getAttribute('data-value');
            console.log('선택된 비율:', selectedValue);
        }

        // 등장인물 이미지 업로드 처리
        function handleCharacterImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // 이미지 파일 검증
            if (!file.type.startsWith('image/')) {
                alert('이미지 파일만 업로드할 수 있습니다.');
                return;
            }

            // 파일 크기 검증 (10MB 제한)
            if (file.size > 10 * 1024 * 1024) {
                alert('파일 크기는 10MB 이하여야 합니다.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('characterPreview');
                const uploadBox = document.getElementById('characterUploadBox');

                preview.src = e.target.result;
                uploadBox.classList.add('has-image');

                console.log('이미지 업로드 완료:', file.name);
            };
            reader.readAsDataURL(file);
        }

        // 이미지 삭제
        function removeCharacterImage(event) {
            event.stopPropagation();

            const preview = document.getElementById('characterPreview');
            const uploadBox = document.getElementById('characterUploadBox');
            const input = document.getElementById('characterImageInput');

            preview.src = '';
            uploadBox.classList.remove('has-image');
            input.value = '';

            console.log('이미지 삭제 완료');
        }

        // 페이지 로드 시 초기화
        document.addEventListener('DOMContentLoaded', function() {
            // 기본값으로 16:9 선택
            const defaultOption = document.querySelector('#ratioOptions .option-card[data-value="16:9"]');
            if (defaultOption) {
                defaultOption.classList.add('selected');
            }
        });
    </script>

</body>
</html>
